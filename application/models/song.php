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

}