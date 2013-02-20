<?php

class Inbox_Controller extends Base_Controller {

	public $restful = true;

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
	
	public function get_import() {
	
		if (Request::ajax()) {
			$file = Input::get('file');
				
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
				echo '<p><span class="label label-warning">Must be converted</span> ' . $song['filename'] . '</p>';
	
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
	
					$tagwriter = new getID3_write_id3v2;
					$tagwriter->filename       = $fullpath;
					$tagwriter->tagformats     = array('id3v2.3');
					$tagwriter->merge_existing_data = false;
					$tagwriter->overwrite_tags = true;
					$tagwriter->tag_encoding   = "UTF-8";
					$tagwriter->remove_other_tags = true;
					
					$TagData['TPE1'][0]['data']  = $song['artist'];
					$TagData['TIT2'][0]['data']  = $song['title'];
					
					$TagData['TCON'][0]['data']  = $song['metadata']['genre'];
					$TagData['TYER'][0]['data']  = $song['metadata']['year'];
					$TagData['TBPM'][0]['data']  = $song['metadata']['bpm'];
					
					$TagData['TXXX'][0]['description']  = 'Acoustid Id';
					$TagData['TXXX'][0]['data']  = $song['metadata']['acoustid_acoustid'];
			
					$TagData['TXXX'][1]['description']  = 'Acoustid Fingerprint';
					$TagData['TXXX'][1]['data']  = $song['metadata']['acoustid_fingerprint'];

					$TagData['TXXX'][2]['description']  = 'Acoustid Score';
					$TagData['TXXX'][2]['data']  = $song['metadata']['acoustid_score'];
								
					$tagwriter->tag_data = $TagData;
										
					if ($tagwriter->WriteID3v2()) {
						
						$hash = hash('sha256', $song['metadata']['acoustid_acoustid'] . $song['metadata']['acoustid_fingerprint']);
												
						if (DB::table('hash_acoustid_fingerprint')->where('hash', '=', $hash)->count() == 0) {
							
							$new_librarysong_id = Librarysong::create($song);
							DB::table('hash_acoustid_fingerprint')->insert(array('library_id' => $new_librarysong_id, 'hash' => $hash));
							
							$new_song = Librarysong::find($new_librarysong_id);

							$new_artist = dkHelpers::remove_bad_characters($song['artist']);
							$new_title = dkHelpers::remove_bad_characters($song['title']);

							$new_filename = $new_artist . ' - ' . $new_title . '.mp3';
							$new_filename = dkHelpers::move_file( $fullpath, dkmusic_library . dkHelpers::get_folder_prefix($new_filename) . DS . $new_filename );
							
							$new_song->filename = $new_filename;
							$new_song->folder = dkHelpers::get_folder_prefix($new_filename);
							$new_song->save();
							
							echo '<p><span class="label label-success">IMPORTED</span> ' . $new_filename .'</p>';
							
						} else {
							
							dkHelpers::move_file( $fullpath, dkmusic_output_duplicates . $song['filename'] );
							echo '<p><span class="label label-important">DUPLICATE</span> ' . $song['filename'] . '</p>';
							
						}
		
					} else {
						echo '<p><span class="label label-warning">WARNING</span> Failed to write TAG data to file</p>';
					}
					
					
				} else {
					dkHelpers::move_file( $fullpath, dkmusic_internal_missingdata . $song['filename'] );
					echo '<p><span class="label label-warning">Missing data</span> ' .$song['filename'] . '</p>';
					
				}
			} else {
				dkHelpers::move_file( $fullpath, dkmusic_trash . $song['filename'] );
				echo '<p><span class="label label-important">Moved to trash</span> ' . $song['filename'] . '</p>';
			}

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