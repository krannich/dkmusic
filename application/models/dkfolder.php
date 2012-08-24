<?php


class dkFolder {
	
	public static function get_size_of($path) {
	
	  if(!file_exists($path)) return 0;
	  if(is_file($path)) return filesize($path);
	  $foldersize = 0;
	  foreach(glob($path."/*") as $fn) $foldersize += foldersize($fn);
	  return $foldersize;
	}
	
	
	public static function count_files_in($path) {
	  	if(!file_exists($path)) return 0;
		if ($hndDir = opendir($path)) {
			$intCount = 0;
			while (false !== ($strFilename = readdir($hndDir)))	{
				if (is_file($path.$strFilename) && $strFilename != "." && $strFilename != ".." && $strFilename != ".DS_Store") {
					$intCount++;
				}
			}
			closedir($hndDir);
		} else {
			$intCount = -1;
		}
		return $intCount;
	}
	
	public static function get_filelist_of($path) {
		$filelist = array();
		if (is_dir($path)) {
			$dir = opendir($path);
			while (false !== ($file = readdir($dir))) {
				$fileextension = end(explode(".", $file));
				if ($fileextension == "mp3" || $fileextension =="m4a" || $fileextension == "m4p") $filelist[] = $file;
			}
			closedir($dir);
		}
		return $filelist;
	}

	
	
}