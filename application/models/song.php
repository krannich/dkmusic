<?php

class Song extends Eloquent {
	
	public static $timestamps = true;
		
	public static function retrieve_acoustid_data($fingerprint, $length) {
		$acoustid_data = array();			
		$acoustid_data['acoustid']="";
		$acoustid_data['score'] = "0";

		$duration = dkHelpers::convert_to_seconds($length);

		if ($fingerprint != "" && $duration > 10) {
			$json_data = @file_get_contents("http://api.acoustid.org/v2/lookup?client=" . AcoustID_API_KEY . "&duration=" . $duration . "&fingerprint=" . $fingerprint);
			$json_obj = json_decode($json_data);
			
			if (is_object($json_obj) && count($json_obj->results)>0) {
				$acoustid_data['acoustid'] = $json_obj->results[0]->id;
				$acoustid_data['score'] = $json_obj->results[0]->score;
			} 
		}

		return $acoustid_data;
	}

	public static function create_fingerprint($fullpath) {
		$fullpath = dkHelpers::well_formed_fullpath($fullpath);
		$fpcalc_data = exec('./bin/fpcalc "' . $fullpath . '"');
		$fingerprint = str_replace("FINGERPRINT=", "", $fpcalc_data);
		if ($fingerprint == "AQAAAA") $fingerprint = "";
		return $fingerprint;
	}

	public static function create($song) {
		$metadata = array();
		$timestamp = date("Y-m-d H:i:s");

		if (array_key_exists('metadata', $song)) {
			$metadata = $song['metadata'];
			unset($song['metadata']);
		}
		$song['created_at'] = $timestamp;
		$song['updated_at'] = $timestamp;
		
		$song_id = DB::table(static::$table)->insert_get_id($song);

		$metadata[static::$table . '_id'] = $song_id;
		$metadata['created_at'] = $timestamp;
		$metadata['updated_at'] = $timestamp;

		$song_metadata_id = DB::table( static::$table . "_metadata" ) -> insert_get_id($metadata);

		return $song_id;
	}
	
	public function get_metadata() {
		$song_metadata = DB::table(static::$table.'_metadata')->where(static::$table.'_id', '=', $this->id)->first();
		return $song_metadata;
	}
	
	public static function write_mp3tags($fullpath, $song) {
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
		
		return $tagwriter->WriteID3v2();
		
	}

}