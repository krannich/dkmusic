<?php

/*

!! THIS FILE MIGHT HELP IF YOU HAVE MESSED UP YOUR LIBRARY AND HAVE A BACKUP OF YOUR MUSIC FILES !!

Simply specify the three locations and run the script in your browser.
All files, that are missing in your library will be copied from the backup volume.
Afterwards, start the import process.

*/

function get_directory_content($directory) {
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

function flush_buffers (){
    echo(str_repeat(' ',1024));
    if (ob_get_length()){            
        @ob_flush();
        @flush();
        @ob_end_flush();
    }    
    @ob_start();
}

$local = "/Volumes/Music/Music Library/";
$drobo = "/Volumes/Drobo/Musik/Music Library/";
$inbox = "/Volumes/Music/dkMusic/Inbox/";

$folders = array (
/*
	"#",
	"A",
*/
	"B",
	"C",
	"D",
	"E",
	"F",
	"G",
	"H",
	"I",
	"J",
	"K",
	"L",
	"M",
	"N",
	"O",
	"P",
	"Q",
	"R",
	"S",
	"T",
	"U",
	"V",
	"W",
	"X",
	"Y",
	"Z",
);

foreach ($folders as $folder) {
	echo "<h1>" . $folder . "</h1>";
	$local_filelist = get_directory_content($local.$folder);
	$drobo_filelist = get_directory_content($drobo.$folder);
	
	$results = array_diff($drobo_filelist, $local_filelist);
	foreach ($results as $result) {
		if (!file_exists($inbox.$result)) copy ($drobo.$folder."/".$result, $inbox.$result);
	}
	echo "<p>Copied: " . sizeof($results). "files.<hr />";
	flush_buffers();
}
echo "<h1>Finished!</h1>";
?>