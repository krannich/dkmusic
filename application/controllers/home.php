<?php

class Home_Controller extends Base_Controller {

	public $restful = true;

	public function get_index() {
		
		return View::make('home.index');
	}
	

	public function get_search() {

		if (Request::ajax()) {
			
			$searchstring = Input::get('searchstring');

			if (strlen($searchstring) < 4) die('[]');
			if (substr($searchstring,-1) =="*") $searchstring=substr($searchstring,0,-1);
			if (substr($searchstring,-1) !="%") $searchstring.="%";
			if (substr($searchstring,0,1) == "*") $searchstring = "%".substr($searchstring,1)."%";

			$songs = Librarysong::where('filename', 'LIKE', $searchstring)->get();
			
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
					'playbutton'=>'<a href="'. $html_file_link .'"><img src="img/but_play.png" /></a>',
				);
			}
			
			echo json_encode($results);
			
		} 
			
	}


	

}