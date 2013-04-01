<?php

class Inbox_Controller extends Base_Controller {

	public $restful = true;

	public function get_lala() {
		
		$override = 2;
		$hash = "00025707383b0612c84522f18a34e7f54b9639a7f1a926e2e971470b74b0ba1f";
		$song['metadata']['bitrate'] = 320;
		
		if ($lib_id = DB::table('hash_acoustid_fingerprint')->where('hash', '=', $hash)->first()) {
		
			$lib_song = Librarysong::find($lib_id->library_id);
			$lib_song_bitrate = $lib_song->get_metadata()->bitrate;
			
			if ($override==1 && ($lib_song_bitrate<$song['metadata']['bitrate'])) {
				
				echo "Song überschreiben.";
				
			} else if ($override==2) {
				
				echo "Song hinzufügen.";
				
			}
					
		} else {
			
			echo "Song nicht vorhanden, kann importiert werden.";
			
		}
	}

	public function get_index() {
		$inbox_count = count(dkHelpers::get_directory_content(dkmusic_inbox));
		return View::make('inbox.index')->with('inbox_count', $inbox_count);
	}

	public function get_convert_content() {
		$filelist = dkHelpers::get_directory_content(dkmusic_internal_convert);
		return json_encode($filelist);
	}
	
	public function get_emptytrash() {
		self::_recursiveDelete(dkmusic_trash);
		return Redirect::to('/inbox')->with('success', 'Trash successfully deleted!');
	}

	public function get_convert() {
		if (Request::ajax()) {
			$file = Input::get('file');
			$filename = substr($file, 0, -4);
			
			$input_file = dkmusic_internal_convert . $filename . ".m4a";
			$output_file = dkmusic_internal_convert . $filename . ".mp3";

			$convert_data = exec ('./bin/ffmpeg -i "' . $input_file . '" -acodec mp3 -ac 2 -ab 320 "' . $output_file . '"');
			
			unlink ($input_file);
			dkHelpers::move_file( $output_file, dkmusic_inbox . $filename . ".mp3" );
			
			echo '<p><span class="label label-success">CONVERTED</span> ' . $filename . '</p>';

		}

	}

	public function get_inbox_content() {
		$filelist = dkHelpers::get_directory_content(dkmusic_inbox);
		return json_encode($filelist);
	}
	
	public function get_info() {
		if (Request::ajax()) {
			$info = array(
				"inbox_count" 			=> dkFolder::count_files_in(dkmusic_inbox),
				"convert_count" 		=> dkFolder::count_files_in(dkmusic_internal_convert),
				"missingdata_count" 	=> dkFolder::count_files_in(dkmusic_internal_missingdata),
				"duplicates_count" 		=> dkFolder::count_files_in(dkmusic_output_duplicates),
				"trash_count" 			=> dkFolder::count_files_in(dkmusic_trash),
			); 
			return Response::json($info); 
		}
	}
	
	public function get_import() {

		if (Request::ajax()) {
			$response = array();

			$file = Input::get('file');
			$override = Input::get('override');
				
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
	
			$song['filename'] = $file;	    
	        $fullpath = dkmusic_inbox . $song['filename'];

			$song['type'] = strtolower(File::extension($song['filename']));
			$song['size'] = filesize($fullpath);
	
			if ($song['type'] == "m4a") {
				dkHelpers::move_file( $fullpath, dkmusic_internal_convert . $song['filename'] );
				$response = array (
					'code' => 2,
					'text' => '<p><span class="label label-warning">Must be converted</span> ' . $song['filename'] . '</p>'
				);
					
			} else if ($song['type'] == "mp3") {
				$fileinfo = $getid3->analyze($fullpath);
											
				if (!empty($fileinfo['id3v2']['comments']['artist'][0]) && !empty($fileinfo['id3v2']['comments']['title'][0])) {

					$song['artist'] = dkHelpers::well_formed_artist($fileinfo['id3v2']['comments']['artist'][0]);
					$song['title'] = dkHelpers::well_formed_string($fileinfo['id3v2']['comments']['title'][0]);
				
					$song['metadata'] = self::_get_metadata($fileinfo);
		
					$fingerprint = Song::create_fingerprint($fullpath);
					$acoustid_data = Song::retrieve_acoustid_data($fingerprint, $song['metadata']['length']);
		
					$song['metadata']['acoustid_fingerprint'] = $fingerprint;
					$song['metadata']['acoustid_acoustid'] = $acoustid_data['acoustid'];
					$song['metadata']['acoustid_score'] = $acoustid_data['score'];
					
					$hash = hash('sha256', $song['metadata']['acoustid_acoustid'] . $song['metadata']['acoustid_fingerprint']);


					if (Song::write_mp3tags($fullpath, $song)) {
			
						if ($lib_id = DB::table('hash_acoustid_fingerprint')->where('hash', '=', $hash)->first()) {
						
							$lib_song = Librarysong::find($lib_id->library_id);
							$lib_song_bitrate = $lib_song->get_metadata()->bitrate;
							
							if ($override==1 && ($lib_song_bitrate<=$song['metadata']['bitrate'])) {
								/*
								dkHelpers::move_file(
									dkmusic_library . $lib_song->folder . DS . $lib_song->filename,
									dkmusic_trash . $lib_song->filename
								);
								
								$new_filename = $song['artist'] . ' - ' . $song['title'] . '.mp3';
								$new_filename = dkHelpers::move_file( $fullpath, dkmusic_library . dkHelpers::get_folder_prefix($new_filename) . DS . $new_filename );
								$song['filename'] = $new_filename;
								$song['folder'] = dkHelpers::get_folder_prefix($new_filename);
								$lib_song->fill($song);
								$lib_song->save();
								
								$response = array (
									'code' => 1,
									'text' => '<p><span class="label label-success">OVERRIDE</span> ' . $new_filename . ' (<b>DB: ' . $lib_song_bitrate . '</b>/' . $song['metadata']['bitrate'] . 'kbit/s)</p>'
								);
								*/
								
								$response = array (
									'code' => 1,
									'text' => '<p><span class="label label-warning">ERROR</span> NOT IMPLEMENTED YET</p>'
								);

								
							} else if ($override == 2 && ($lib_song_bitrate < $song['metadata']['bitrate'])) {

								$new_filename = $song['artist'] . ' - ' . $song['title'] . '.mp3';
								$new_filename = dkHelpers::move_file( $fullpath, dkmusic_library . dkHelpers::get_folder_prefix($new_filename) . DS . $new_filename );
								$song['filename'] = $new_filename;
								$song['folder'] = dkHelpers::get_folder_prefix($new_filename);
								
								$new_librarysong_id = Librarysong::create($song);
								DB::table('hash_acoustid_fingerprint')->insert(array('library_id' => $new_librarysong_id, 'hash' => $hash));
							
								$response = array (
									'code' => 1,
									'text' => '<p><span class="label label-success">ADDED</span> ' . $new_filename . ' (<b>DB: ' . $lib_song_bitrate . '</b>/' . $song['metadata']['bitrate'] . 'kbit/s)</p>'
								);
								
							} else {
							
								dkHelpers::move_file( $fullpath, dkmusic_output_duplicates . $song['filename'] );
								$response = array (
									'code' => 4,
									'text' => '<p><span class="label label-important">DUPLICATE</span> ' . $song['filename'] . '</p>'
								);
							}
									
						} else {
							
							$new_filename = $song['artist'] . ' - ' . $song['title'] . '.mp3';
							$new_filename = dkHelpers::move_file( $fullpath, dkmusic_library . dkHelpers::get_folder_prefix($new_filename) . DS . $new_filename );
							$song['filename'] = $new_filename;
							$song['folder'] = dkHelpers::get_folder_prefix($new_filename);
							
							$new_librarysong_id = Librarysong::create($song);
							DB::table('hash_acoustid_fingerprint')->insert(array('library_id' => $new_librarysong_id, 'hash' => $hash));
							
							$response = array (
								'code' => 1,
								'text' => '<p><span class="label label-success">IMPORTED</span> ' . $new_filename .'</p>'
							);
							
							
						}

		
					} else {
					
						$response = array (
							'code' => 99,
							'text' => '<p><span class="label label-warning">WARNING</span> Failed to write TAG data to file</p>'
						);
					}
					
				} else {
					dkHelpers::move_file( $fullpath, dkmusic_internal_missingdata . $song['filename'] );
					$response = array (
						'code' => 3,
						'text' => '<p><span class="label label-warning">Missing data</span> ' .$song['filename'] . '</p>'
					);
					
				}
				
			} else {
				dkHelpers::move_file( $fullpath, dkmusic_trash . $song['filename'] );
				$response = array (
					'code' => 10,
					'text' => '<p><span class="label label-important">Moved to trash</span> ' . $song['filename'] . '</p>'
				);
				
			}
			
			echo json_encode($response);

		}
	}
	
	
	private function _get_metadata($fileinfo) {
		$result = array();

		$result['genre'] 	= (!empty($fileinfo['id3v2']['comments']['genre'][0]) ) ? dkHelpers::well_formed_string($fileinfo['id3v2']['comments']['genre'][0])	: "";
		$result['year'] 	= (!empty($fileinfo['id3v2']['comments']['year'][0]) )  ? $fileinfo['id3v2']['comments']['year'][0] 								: "";
		$result['bpm'] 		= (!empty($fileinfo['id3v2']['comments']['bpm'][0]) )  	? $fileinfo['id3v2']['comments']['bpm'][0] 									: "";
	        
		$result['length'] 	= (!empty($fileinfo['playtime_string'])) 				? $fileinfo['playtime_string'] : '0:00';
	    $result['bitrate'] 	= (!empty($fileinfo['audio']['bitrate'])) 				? round($fileinfo['audio']['bitrate']/1000) : 0;

	    return $result;

	}
	
	private function _recursiveDelete($str){
	    if(is_file($str)){
	        return @unlink($str);
	    }
	    elseif(is_dir($str)){
	        $scan = glob(rtrim($str,'/').'/*');
	        foreach($scan as $index=>$path){
	            self::_recursiveDelete($path);
	        }
	        // return @rmdir($str);
	        return true;
	    }
	}

	

}