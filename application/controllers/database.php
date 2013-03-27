<?php

class Database_Controller extends Base_Controller {

	public $restful = true;

	public function get_index() {
		return View::make('database.index');
	}
	
	public function get_databaseinfo() {
		if (Request::ajax()) {

			$results = array();
	
			$path = dkmusic_library;
			if (!$dir = @opendir($path)) {
				die("Unable to open $path"); 
			}
	
			if (!is_readable($path)) {
				die("Read access denied to $path"); 
			} else {
    			while ($dir && $file = readdir($dir)) {

					if (is_dir("$path/$file")  &&  $file[0]!='.') {
						$results[] = array(
							'foldername' => $file,
							'folder_checkbox' => '<input type="checkbox" name="foldername" value="' . $file . '" /><span id="folder' . ($file == "#" ? '_N' : $file) . '"></span>',
							'files_count' => dkHelpers::count_files_in_dir($path.$file.'/'),
							'acoustid_fingerprint_count' => Librarysong::count_all_with_acoustid_fingerprint($file),
							'acoustid_acoustid_count' => Librarysong::count_all_with_acoustid($file),
							'folder_size' => Librarysong::count_folder_size_of($file),
						);
					
					}
				}
			}
			
			echo json_encode($results);
		}
		
	}
	
	
	public function get_renamefiles() {
		if (Request::ajax()) {
			
			$success = 1;
			$results = array();

			if ($folder = Input::get('folder') ) {
				
				$files = DB::query('select * FROM library where folder = "'. $folder .'" AND CONCAT(artist," - ",title, ".mp3") != filename ORDER BY id');
								
				foreach ($files as $file) {
					$file_id = $file->id;
					$orig_filename = $file->filename;
					$new_filename = dkHelpers::well_formed_artist($file->artist) . ' - ' . dkHelpers::well_formed_string($file->title) . '.mp3';
					
					$orig_fullpath = dkmusic_library . $folder . DS . $orig_filename;
					$new_fullpath = dkmusic_library . dkHelpers::get_folder_prefix($new_filename) . DS . $new_filename;
					
					if (file_exists($orig_fullpath)) {
					
						$return_filename = dkHelpers::move_file( $orig_fullpath, $new_fullpath );
	
						DB::table('library')->where('id', '=', $file_id)->update(array('filename' => $return_filename));
	
						if ($return_filename == $new_filename) {						
							$results[] = '<p><span class="label label-success">OK</span> ' . $new_fullpath . '</p>';
						} else {
							$results[] = '<p><span class="label label-warning">ERROR</span> There is another file of this artist with the same title.<br />' . $return_filename . '</p>';						
						}
					
					} else {
						$results[] = '<p><span class="label label-warning">ERROR</span> File does not exist.<br />' . $return_filename . '</p>';						
					}
					
					
				}				
				
			}
			
			$data = array();
			$data['success'] = $success;
			$data['files'] = $results;

			header( 'Content-type: application/json' );
			print json_encode( $data );
			
		}

	}

	
	
	public function get_scanfornewfiles() {
		if (Request::ajax()) {
			
			$success = 1;
			$results = array();

			if ($folder = Input::get('folder') ) {
				$librarysongs = Librarysong::get_filelist_of_folder($folder);
				$filelist = dkHelpers::get_directory_content(dkmusic_library.$folder);
				$results = array_diff($filelist, $librarysongs);
				if (count($results) > 0) {
					foreach ($results as $filename) {
						dkHelpers::move_file( dkmusic_library . $folder . DS . $filename, dkmusic_inbox . $filename );
					}
				}
			}
			
			$data = array();
			$data['success'] = $success;
			$data['files'] = $results;

			header( 'Content-type: application/json' );
			print json_encode( $data );
			
		}

	}


	public function get_scanforremovedfiles() {
		if (Request::ajax()) {

			$success = 1;
			$results = array();

			if ($folder = Input::get('folder') ) {
				$librarysongs = Librarysong::get_filelist_of_folder($folder);
				$filelist = dkHelpers::get_directory_content(dkmusic_library.$folder);
				$results = array_diff($librarysongs, $filelist);
				if (count($results) > 0) {
					foreach ($results as $filename) {
						$song = DB::table('library')->where('filename', '=', $filename)->first('id');
						$song_id = $song->id;
						DB::table('library')->where('id', '=', $song_id)->delete();
						DB::table('library_metadata')->where('library_id', '=', $song_id)->delete();
						DB::table('hash_acoustid_fingerprint')->where('library_id', '=', $song_id)->delete();
					}
				}
			} 
			
			$data = array();
			$data['success'] = $success;
			$data['files'] = $results;

			header( 'Content-type: application/json' );
			print json_encode( $data );
			
		}

	}




	public function get_rescan() {
		if (Request::ajax()) {

			if ($folder = Input::get('folder') ) {
			
				//$results = Librarysong::get_filelist_of_folder($folder);
				$results = Librarysong::get_filelist_of_incorrect_acoustid_in_folder($folder);

				if (count($results) > 0) {

					foreach ($results as $filename) {
						$song = DB::table('library_metadata')
								->left_join ('library', 'library.id', '=', 'library_metadata.library_id')
								->where('library.filename', '=' ,$filename)
								->first(array('library.id','library.filename','library_metadata.length','library_metadata.acoustid_acoustid', 'library_metadata.acoustid_score', 'library_metadata.acoustid_fingerprint'));

						$file_id = $song->id;
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
					
					    $fullpath = dkmusic_library . dkHelpers::get_folder_prefix($song->filename) . '/' . $song->filename;
						if (!file_exists($fullpath)) return;

						$fileinfo = $getid3->analyze($fullpath);
						
						if (!empty($fileinfo['id3v2']['comments']['artist'][0]) && !empty($fileinfo['id3v2']['comments']['title'][0])) {

							$new_song['metadata'] = self::_get_metadata($fileinfo);
				
							$fingerprint = Song::create_fingerprint($fullpath);
							$acoustid_data = Song::retrieve_acoustid_data($fingerprint, $new_song['metadata']['length']);
						
							$orig_fingerprint = $song->acoustid_fingerprint;
							$orig_acoustid = $song->acoustid_acoustid;
				
							if ( $orig_fingerprint != $fingerprint || $orig_acoustid != $acoustid_data['acoustid'] ) {
						
								$new_song['artist'] = dkHelpers::well_formed_artist($fileinfo['id3v2']['comments']['artist'][0]);
								$new_song['title'] = dkHelpers::well_formed_string($fileinfo['id3v2']['comments']['title'][0]);
								
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
									
									$hash = hash('sha256', $new_song['metadata']['acoustid_acoustid'] . $new_song['metadata']['acoustid_fingerprint']);
									DB::table('hash_acoustid_fingerprint')->where('library_id', '=', $file_id)->update(array('hash' => $hash));

									echo '<p><span class="label label-success">UPDATE</span> ' . $file . '</p>';
									
								} else {
									echo '<p><span class="label label-warning">ERROR</span> ' . implode('<br><br>', $tagwriter->errors) . '</p>';
								}
								
							}
							
						} else {
							
							dkHelpers::move_file( $fullpath, dkmusic_internal_missingdata . $song->filename );
							echo '<p><span class="label label-warning">Missing data</span> ' . $song->filename . '</p>';
							
						}
			
					}
				}
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

		
	

}