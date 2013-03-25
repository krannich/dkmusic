<?php
/*
		$songs = Librarysong::left_join(self::$table . '_metadata', self::$table . '.id' , '=', self::$table . '_metadata.' . self::$table . '_id')
			->where(self::$table . '_metadata.type', '=', 'm4a');
		return count($songs->get(self::$table . '.id'));

*/



class Librarysong extends Song {

	public static $table = "library";

	public static function get_filelist_of_incorrect_acoustid_in_folder($folder = "") {
		$entries = DB::table(self::$table)
			->where('library.folder' ,'=', $folder)
			->left_join ('library_metadata', 'library.id', '=', 'library_metadata.library_id')
			->where(DB::RAW('length(library_metadata.acoustid_acoustid)'), '<' , '36')
			->where('library.type', '=' , 'mp3')
			->get('library.filename');
		$results = array();
		foreach($entries as $entry) {
			$results[]=$entry->filename;
		}
		return $results;		
	}


	public static function count_all() {
		return Librarysong::count('id');
	}
	
	public static function count_all_m4a() {
		return DB::table(self::$table . '_metadata')->where('type' ,'=', 'm4a')->count('library_id');
	}
	
	public static function count_all_m4p() {		
		return DB::table(self::$table . '_metadata')->where('type' ,'=', 'm4p')->count('library_id');
	}

	public static function count_all_mp3() {
		return DB::table(self::$table . '_metadata')->where('type' ,'=', 'mp3')->count('library_id');
	}

	
	public static function count_all_with_acoustid($folder = "") {
	
		if ($folder == "") {
			return DB::table(self::$table . '_metadata')->where_not_null('acoustid_acoustid')->count('library_id');
		} else {
			$folder = strtoupper($folder);
			
			return DB::table( self::$table )
				->left_join( self::$table . '_metadata', self::$table . '.id', '=', self::$table . '_metadata.' . self::$table . '_id' )
				->where(self::$table . '.folder', '=', $folder)
				->where_not_null('acoustid_acoustid')
				->count(self::$table . '.id');

		}
		
	}

	public static function count_all_with_acoustid_fingerprint($folder = "") {
		
		if ($folder == "") {
			return DB::table(self::$table . '_metadata')->where_not_null('acoustid_fingerprint')->count('library_id');
		} else {
			$folder = strtoupper($folder);
			
			return DB::table( self::$table )
				->left_join( self::$table . '_metadata', self::$table . '.id', '=', self::$table . '_metadata.' . self::$table . '_id' )
				->where(self::$table . '.folder', '=', $folder)
				->where_not_null('acoustid_fingerprint')
				->count(self::$table . '.id');

		}
		
	}
	
	public static function count_folder_size_of($path ="") {
		if ($path == '') {
			return dkHelpers::format_size(Librarysong::sum('size'));
		
		} else {
			return dkHelpers::format_size(Librarysong::where("folder", "=", $path)->sum('size'));
		}
		
	}
	
	public static function get_filelist_of_folder($folder = "") {
		$entries = DB::table(self::$table)->where('folder' ,'=', $folder)->get('filename');
		$results = array();
		foreach($entries as $entry) {
			$results[]=$entry->filename;
		}
		return $results;		
	}
	
	public static function get_by_acoustfingerprint($acoustfingerprint) {
		return DB::table(self::$table . '_metadata')->where('acoustid_fingerprint' ,'=', $acoustfingerprint)->get();
	}	
	
	
	/*
	public static function find_duplicates() {
		$songs = Librarysong::left_join(self::$table . '_metadata', self::$table . '.id' , '=', self::$table . '_metadata.' . self::$table . '_id')
			->where_not_null(self::$table . '_metadata.acoustid_fingerprint')
			->group_by(self::$table . '_metadata.acoustid_fingerprint')
			->get(self::$table . '_metadata.acoustid_fingerprint');
		return $songs;
	}
	*/
	
	
}