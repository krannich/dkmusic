<?php

class Library_Controller extends Base_Controller {

	public $restful = true;

	public function get_index() {
		return View::make('library.index');
	}
	
	public function delete_song($id) {
		$song = Librarysong::find($id);
		DB::table('library')->where('id', '=', $id)->delete();
		DB::table('library_metadata')->where('library_id', '=', $id)->delete();
		dkHelpers::move_file( dkmusic_library . dkHelpers::get_folder_prefix($song->filename) . '/' . $song->filename, dkmusic_trash . $song->filename );
		return "[]";
	}
	
	public function put_song($id) {
		
		$input = Input::json();
		
		$artist = dkHelpers::well_formed_artist($input->artist);
		$title = dkHelpers::well_formed_string($input->title);
		
		$song = LibrarySong::find($id);		
		$filename = $song->filename;
		
		$song_metadata = $song->get_metadata();
		
		$song->artist = $artist;
		$song->title = $title;

		$new_filename = $artist . ' - ' . $title . '.mp3';

		if ($filename!=$new_filename) {
			$new_filename = dkHelpers::move_file(
				dkmusic_library . dkHelpers::get_folder_prefix($filename) . DS . $filename,
				dkmusic_library . dkHelpers::get_folder_prefix($new_filename) . DS . $new_filename
			);
			$song->filename = $new_filename;
		}

		$song->save();
	
		/*
		METADATA ARE NOT SAVED RIGHT NOW!!!

		$song_metadata->genre = $input->genre;
		$song_metadata->year = $input->year;
		$song_metadata->save();
		*/
		
		/*
		UPDATE ID3 Tags
		*/
		$getid3 = new getID3;	
		$tagwriter = new getID3_write_id3v2;
		$tagwriter->filename       = dkmusic_library . dkHelpers::get_folder_prefix($song->filename) . DS . $song->filename;
		$tagwriter->tagformats     = array('id3v2.3');
		$tagwriter->merge_existing_data = false;
		$tagwriter->overwrite_tags = true;
		$tagwriter->tag_encoding   = "UTF-8";
		$tagwriter->remove_other_tags = true;
		
		$TagData['TPE1'][0]['data']  = $song->artist;
		$TagData['TIT2'][0]['data']  = $song->title;
		
		$tagwriter->tag_data = $TagData;
		$tagwriter->WriteID3v2();


		$html_file_link = addslashes(dkHelpers::get_folder_prefix($song->filename) . DS . rawurlencode($song->filename));		

		$result = array(
			'id'=>$song->id,
			'filename'=>$song->filename,
			'artist'=>$song->artist,
			'title'=>$song->title,
			'length'=>$song_metadata->length,
			'bitrate'=>$song_metadata->bitrate,
			'size'=>dkHelpers::format_size($song->size),
			'html_file_link' => $html_file_link,
		);

		echo json_encode($result);

	}
	

	public function get_search() {

		if (Request::ajax()) {
			
			$searchstring = Input::get('searchstring');
			$searchdate = Input::get('searchdate');

			//$searchstring = "Andre rieu";

			if (strlen($searchstring) < 4 && strlen($searchdate) == 0 ) die('[]');
			if (strlen($searchstring) == 0 && strlen($searchdate) < 10 ) die('[]');

			if (substr($searchstring,-1) =="*") $searchstring=substr($searchstring,0,-1);
			if (substr($searchstring,-1) !="%") $searchstring.="%";
			if (substr($searchstring,0,1) == "*") $searchstring = "%".substr($searchstring,1)."%";

			if (strlen($searchstring) >= 4 && strlen($searchdate) == 0) {
				$songs = Librarysong::where('filename', 'LIKE', $searchstring)->get();
			} else if (strlen($searchstring) == 0 && strlen($searchdate) == 10 ) {
				$songs = Librarysong::where('created_at', '>=', $searchdate)->get();
			} else {
				$songs = Librarysong::where('created_at', '>=', $searchdate)
					->where('filename', 'LIKE', $searchstring)->get();
			}
			
			$results = array();
			
			foreach ($songs as $song) {
			
				$song_metadata = $song->get_metadata();
				
				$html_file_link = addslashes(dkHelpers::get_folder_prefix($song->filename) . DS . rawurlencode($song->filename));		

				$results[] = array(
					'id'=>$song->id,
					'filename'=>$song->filename,
					'artist'=>$song->artist,
					'title'=>$song->title,
					'length'=>$song_metadata->length,
					'bitrate'=>$song_metadata->bitrate,
					'size'=>dkHelpers::format_size($song->size),
					'html_file_link' => $html_file_link,
				);
			}
			
			echo json_encode($results);
			
		} 
			
	}


	

}