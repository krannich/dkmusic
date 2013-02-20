<?php

class dkHelpers {


	/*
	----- Converters ------
	*/

	public static function limit_output_of_string($string, $max) {
		if ( strlen($string) > $max ) {
			return substr($string,0,$max).'...';
		} else {
			return $string;
		}
	}
	
	public static function format_size($size) {
		$sizes = array(" bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		if ($size == 0) { 
			return('0 bytes');
		} else {
			return (round($size/pow(1000, ($i = floor(log($size, 1000)))), $i > 1 ? 2 : 0) . $sizes[$i]);
		}
	}

	public static function convert_to_seconds($length) {
		$length_min = explode(':', $length);
		$duration = $length_min[1]+(60*$length_min[0]);
		return $duration;
	}
	
	public static function get_folder_prefix($folder) {
		$folder = substr($folder,0,1);
		if ($folder == '0' || $folder == '1' || $folder == '2' || $folder == '3' || $folder == '4' || $folder == '5' || $folder == '6' || $folder == '7' || $folder == '8' || $folder == '9') {
			return '#';
		} else {
			return $folder;
		}
	}
	
	public static function well_formed_artist($artist) {
		mb_internal_encoding('UTF-8');
		$artist = mb_strtolower($artist);
		
		$prefix = "die ";
		if (substr($artist, 0, strlen($prefix)) == $prefix) $artist = substr($artist, strlen($prefix), strlen($artist) );

		$prefix = "the ";
		if (substr($artist, 0, strlen($prefix)) == $prefix) $artist = substr($artist, strlen($prefix), strlen($artist) );

		$artist = self::well_formed_string($artist);
		return $artist;
	}
	
	
	public static function well_formed_string($string) {
		mb_internal_encoding('UTF-8');
		$string = mb_strtolower($string);
		$replace_characters = array(
			'ä' => 'ae',
			'ö' => 'oe',
			'ü' => 'ue',
			
			'ß' => 'ss',
			'$' => 's',
			
			'_' => ' ',
			
			"'" => '',
			"´" => '',
			"`" => "",
			
			"[" => "(",
			"]" => ")",
			"?" => "",
			"!" => "",
			'/' => "-",
			'\\' => "-",

			"featuring" => "ft",
			" feat. " => " ft ",
			" feat " => " ft ",
			" ft. " => " ft ",
			"." => "",
			"," => "",		
			
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ã' => 'a',
			
			'ç' => 'c',
			
			'è' => 'e',
			'é' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			
			'ñ' => 'n',
			
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			
			'ý' => 'y',
			'ÿ' => 'y',

			'$' => 's',
			'+' => '&',
	
		);
		$string = strtr( $string, $replace_characters );
		$string = trim($string);
		return ucfirst($string);
	}
	
	
		
	
	
	public static function well_formed_fullpath($string) {
		$replace_characters = array(
			"$" => "\$",
			"´" => "\´",
			'`' => "\`",
			'"' => '\"',
			'?' => '\?',
			'!' => '\!',
		);
		return strtr( $string, $replace_characters );
	}

	public static function remove_bad_characters($string) {
		$replace_characters = array(
			"/" => "-",
		);
		return strtr( $string, $replace_characters );
	}


	/*
	----- Path Operations -----
	*/


	public static function count_files_in_dir($directory) {
		return count(glob($directory . "*"));
	}
	
	public static function get_directory_content($directory) {
		$results = array();	
		if ($dir = @opendir($directory)) {
			while (false !== ($file = readdir($dir))) {
				if ($file != '.' && $file != '..' && $file != '.DStore' && $file != '.DS_Store') {
					$results[] = $file;
				}
			}
		}
		return $results;
	}
	
	public static function move_file($file_from, $file_to) {
		if (file_exists($file_to)) {
			$file_to = substr( $file_to, 0, -4 ) . ' - ' . time() . '.' . File::extension( $file_to );
		} 
		rename( $file_from, $file_to );
		return basename($file_to);
	}


	public static function create_path($pathname) {

	    if (is_dir($pathname) || empty($pathname)) {
	        return true;
	    } 
	 
	    $pathname = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pathname);
	 
	    if (is_file($pathname)) {
	        trigger_error('mkdirr() File exists', E_USER_WARNING);
	        return false;
	    } 
	 
	    $next_pathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
	 
	    if (self::create_path($next_pathname)) {
	        if (!file_exists($pathname)) {
	            return mkdir($pathname);
	        }
	    } 
	 
	    return false;
			
	}
	
	
	



	
	
}