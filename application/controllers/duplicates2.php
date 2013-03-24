<?php

class Duplicates2_Controller extends Base_Controller {

	public $restful = true;

	public function get_index() {
	
		return View::make('duplicates2.index');
		
	}
	
	public function get_remove() {

		if (Request::ajax()) {
		
			$file_id = Input::get('file');

			$song = Librarysong::find($file_id);
			
			DB::table('library')->where('id', '=', $file_id)->delete();
			DB::table('library_metadata')->where('library_id', '=', $file_id)->delete();
			
			dkHelpers::move_file( dkmusic_library . dkHelpers::get_folder_prefix($song->filename) . '/' . $song->filename, dkmusic_trash . $song->filename );

			echo '<p><span class="label label-success">DELETED</span> ' . ($song->filename . ' (' . $song->id . ') moved to trash folder</p>');

		}		
	}
	
	public function get_dupartisttitle() {
		
		if (Request::ajax()) {
		
			$songs = DB::query('
				select artist, title, count(*) duplicates 
				from library 
				group by artist, title 
				having duplicates > 1
				order by duplicates asc;
			');

			$results = array();
			foreach ($songs as $song) {
				$results[] = array(
					'artist'=>$song->artist,
					'title'=>$song->title,
					'duplicates'=>$song->duplicates,
				);
			}
			return json_encode($results);
		} 
	}
	
	
	
	
	public function get_dupacoustids() {
		
		if (Request::ajax()) {
		
			$songs = DB::query('
				SELECT library.filename, library_metadata.acoustid_acoustid, count(library_metadata.acoustid_acoustid) as duplicates
				FROM library, library_metadata
				WHERE
				library_metadata.library_id = library.id AND
				library_metadata.acoustid_acoustid IS NOT NULL
				GROUP BY library_metadata.acoustid_acoustid
				HAVING duplicates > 1
				ORDER by duplicates DESC
				LIMIT 50
			');

			$results = array();
			foreach ($songs as $song) {
				$results[] = array(
					'acoustid'=>$song->acoustid_acoustid,
					'acoustid_and_filename'=>$song->acoustid_acoustid . '<br />' . $song->filename,
					'duplicates'=>$song->duplicates,
				);
			}
			return json_encode($results);
		
		} 
		
	}

	/*
	public function get_dupfingerprints() {
		
		if (Request::ajax()) {
		
			$songs = DB::query('
				SELECT library.filename, library_metadata.acoustid_fingerprint, count(library_metadata.acoustid_fingerprint) as duplicates
				FROM library, library_metadata
				WHERE
				library_metadata.library_id = library.id AND
				library_metadata.acoustid_fingerprint IS NOT NULL
				GROUP BY library_metadata.acoustid_fingerprint
				HAVING duplicates > 1
				ORDER by duplicates DESC
				LIMIT 50
			');

			$results = array();
			foreach ($songs as $song) {
				$results[] = array(
					'fingerprint_output'=>substr($song->acoustid_fingerprint, 0,65) . '<br />' . $song->filename,
					'fingerprint'=>$song->acoustid_fingerprint,
					'duplicates'=>$song->duplicates,
				);
			}
			return json_encode($results);
		
		} 
		
	}
	*/
	
	public function get_dup_fingerprints() {
		
		if (Request::ajax()) {
		
			$songs = DB::query('
				SELECT
					library.id,
					library.folder,
					library.filename,
					library.artist,
					library.title,
					library_metadata.bitrate,
					library_metadata.length,
					library_metadata.size,
					library_metadata.library_id,
					library_metadata.acoustid_fingerprint
				FROM library_metadata
				INNER JOIN (
				    SELECT acoustid_fingerprint
				    FROM library_metadata
				    GROUP BY acoustid_fingerprint
				    HAVING count(acoustid_fingerprint) > 1
				) dup ON library_metadata.acoustid_fingerprint = dup.acoustid_fingerprint
				LEFT JOIN library ON library_metadata.library_id=library.id
				ORDER BY library_metadata.acoustid_fingerprint;
			');

			$results = array();
			
			foreach ($songs as $song) {
			
				$html_file_link = addslashes($song->folder.DS.rawurlencode($song->filename));		

				$results[] = array(
					'id'=>$song->id,
					'filename'=>$song->filename,
					'artist'=>$song->artist,
					'title'=>$song->title,
					'size'=>dkHelpers::format_size($song->size),
					'playbutton'=>'<a href="'. $html_file_link .'"><img src="img/but_play.png" title="' . $song->folder . '/' . $song->filename. '"/></a>',
					'bitrate'=>$song->bitrate,
					'length'=>$song->length,
				);
			}

			return json_encode($results);
		
		} 
				
	}



	public function get_showfiles_acoustid() {
		
		if (Request::ajax()) {
		
			$acoustid = Input::get('value');
			
			$songs = DB::query('
				SELECT library_metadata.bitrate, library_metadata.length, library.id, library.artist, library.title, library.filename, library.size, library.type
				FROM library, library_metadata
				WHERE
				library_metadata.library_id = library.id AND
				library_metadata.acoustid_acoustid = "' . $acoustid . '"
				ORDER BY library.filename
			');

			$results = array();
			
			foreach ($songs as $song) {
			
				$html_file_link = addslashes(dkHelpers::get_folder_prefix($song->filename).DS.rawurlencode($song->filename));		

				$results[] = array(
					'id'=>$song->id,
					'filename'=>$song->filename,
					'artist'=>$song->artist,
					'title'=>$song->title,
					'size'=>dkHelpers::format_size($song->size),
					'playbutton'=>'<a href="'. $html_file_link .'"><img src="img/but_play.png" /></a>',
					'bitrate'=>$song->bitrate,
					'length'=>$song->length,
					'type'=>$song->type,
				);
			}

			return json_encode($results);
		
		} 
				
	}
	
	public function get_showfiles_fingerprint() {
		if (Request::ajax()) {
		
			$fingerprint = Input::get('value');
			
			$songs = DB::query('
				SELECT library_metadata.bitrate, library_metadata.length, library.id, library.artist, library.title, library.filename, library.size, library.type
				FROM library, library_metadata
				WHERE
				library_metadata.library_id = library.id AND
				library_metadata.acoustid_fingerprint = "' . $fingerprint . '"
				ORDER BY library.filename;
			');

			$results = array();
			
			foreach ($songs as $song) {
			
				$html_file_link = addslashes(dkHelpers::get_folder_prefix($song->filename).DS.rawurlencode($song->filename));		

				$results[] = array(
					'id'=>$song->id,
					'filename'=>$song->filename,
					'artist'=>$song->artist,
					'title'=>$song->title,
					'size'=>dkHelpers::format_size($song->size),
					'playbutton'=>'<a href="'. $html_file_link .'"><img src="img/but_play.png" /></a>',
					'bitrate'=>$song->bitrate,
					'length'=>$song->length,
					'type'=>$song->type,
				);
			}

			return json_encode($results);
		
		} 
				
	}
	
	
	public function get_rescan() {
		
		if (Request::ajax()) {

			$file_id = Input::get('file');
			
			$song = DB::table('library_metadata')
				->left_join ('library', 'library.id', '=', 'library_metadata.library_id')
				->where('library_metadata.library_id', '=' ,$file_id)
				->first(array('library.filename','library_metadata.length','library_metadata.acoustid_acoustid', 'library_metadata.acoustid_score', 'library_metadata.acoustid_fingerprint'));
		
			$file = $song->filename;
		
		    $getid3 = new getID3;	
		    $getid3->encoding = 'UTF-8';
			$getid3->option_tag_id3v1         	= false;              
		    $getid3->option_tag_lyrics3       	= false;              
		    $getid3->option_tag_apetag        	= false;              
		    $getid3->option_accurate_results  	= false;             
		    $getid3->option_tags_images       	= false;
		    $getid3->option_md5_data			= false;	
		    $getid3->option_md5_data_source		= false;
		    $getid3->option_tags_html 			= false; 
		
		    $fullpath = dkmusic_library . dkHelpers::get_folder_prefix($song->filename) . DS . $song->filename;
			$fileinfo = $getid3->analyze($fullpath);
			$new_song['metadata'] = self::_get_metadata($fileinfo);

			$fingerprint = Song::create_fingerprint($fullpath);
			$acoustid_data = Song::retrieve_acoustid_data($fingerprint, $new_song['metadata']['length']);
		
			$orig_fingerprint = $song->acoustid_fingerprint;
			$orig_acoustid = $song->acoustid_acoustid;

			if ( $orig_fingerprint != $fingerprint || $orig_acoustid != $acoustid_data['acoustid'] ) {
			
				$new_song['artist'] = dkHelpers::well_formed_artist($fileinfo['tags']['id3v2']['artist'][0]);
				$new_song['title'] = dkHelpers::well_formed_string($fileinfo['tags']['id3v2']['title'][0]);
				
				$new_song['metadata']['acoustid_fingerprint'] = $fingerprint;
				$new_song['metadata']['acoustid_acoustid'] = $acoustid_data['acoustid'];
				$new_song['metadata']['acoustid_score'] = $acoustid_data['score'];

				$tagwriter = new getID3_write_id3v2;
				$tagwriter->filename       = $fullpath;
				$tagwriter->tagformats     = array('id3v2.3');
				$tagwriter->merge_existing_data = false;
				$tagwriter->overwrite_tags = true;
				$tagwriter->tag_encoding   = "UTF-8";
				$tagwriter->remove_other_tags = true;
				
				$TagData['TPE1'][0]['data']  = $new_song['artist'];
				$TagData['TIT2'][0]['data']  = $new_song['title'];
				
				$TagData['TCON'][0]['data']  = $new_song['metadata']['genre'];
				$TagData['TYER'][0]['data']  = $new_song['metadata']['year'];
				$TagData['TBPM'][0]['data']  = $new_song['metadata']['bpm'];
				
				$TagData['TXXX'][0]['description']  = 'Acoustid Id';
				$TagData['TXXX'][0]['data']  = $new_song['metadata']['acoustid_acoustid'];
		
				$TagData['TXXX'][1]['description']  = 'Acoustid Fingerprint';
				$TagData['TXXX'][1]['data']  = $new_song['metadata']['acoustid_fingerprint'];

				$TagData['TXXX'][2]['description']  = 'Acoustid Score';
				$TagData['TXXX'][2]['data']  = $new_song['metadata']['acoustid_score'];
							
				$tagwriter->tag_data = $TagData;
				
				if ($tagwriter->WriteID3v2()) {
					$update_array = array('acoustid_fingerprint' => $fingerprint, 'acoustid_acoustid' => $acoustid_data['acoustid'], 'acoustid_score' => $acoustid_data['score']);
					$update_metadata = DB::table('library_metadata')->where('library_id', '=', $file_id)->update($update_array);
					echo '<p><span class="label label-success">UPDATE</span> ' . $file . '</p>';
				} else {
					echo '<p><span class="label label-warning">ERROR</span> ' . implode('<br><br>', $tagwriter->errors) . '</p>';
				}
				
			}

		
		}
		
	}
	
	private function _get_metadata($fileinfo) {
		$result = array();

		if (!empty($fileinfo['tags']['id3v2'])) {
			$result['genre'] 	= (isset($fileinfo['id3v2']['comments']['genre']) 	&& $fileinfo['id3v2']['comments']['genre'][0] 	!= NULL )  	? dkHelpers::well_formed_string($fileinfo['id3v2']['comments']['genre'][0])	: "";
			$result['year'] 	= (isset($fileinfo['id3v2']['comments']['year']) 	&& $fileinfo['id3v2']['comments']['year'][0] 	!= NULL )  	? $fileinfo['id3v2']['comments']['year'][0] 	: "";
			$result['bpm'] 		= (isset($fileinfo['id3v2']['comments']['bpm']) 	&& $fileinfo['id3v2']['comments']['bpm'][0] 	!= NULL )  	? $fileinfo['id3v2']['comments']['bpm'][0] 		: "";
	        
	    } else {
			$result['genre'] 	= "";
			$result['year'] 	= "";
			$result['bpm'] 		= "";
	    }

		$result['length'] = $fileinfo['playtime_string'];
	    $result['bitrate'] = round($fileinfo['audio']['bitrate']/1000);

	    return $result;

	}

	

		
	

}