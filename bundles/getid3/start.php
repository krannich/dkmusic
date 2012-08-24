<?php
	Autoloader::map(array(
		'getID3' => __DIR__.'/library/getid3.php',
		'getID3_write_id3v2' => __DIR__.'/library/write.id3v2.php',
		'getID3_write_id3v1' => __DIR__.'/library/write.id3v1.php',
	));
?>