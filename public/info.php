<?php
/*
$info = array(
	"inbox_count" 			=> dkFolder::count_files_in(dkmusic_inbox),
	"convert_count" 		=> dkFolder::count_files_in(dkmusic_internal_convert),
	"missingdata_count" 	=> dkFolder::count_files_in(dkmusic_internal_missingdata),
	"duplicates_count" 		=> dkFolder::count_files_in(dkmusic_output_duplicates),
	"trash_count" 			=> dkFolder::count_files_in(dkmusic_trash),
); 
*/

$info = array(
	"inbox_count" 			=> 1,
	"convert_count" 		=> 2,
	"missingdata_count" 	=> 1,
	"duplicates_count" 		=> 100,
	"trash_count" 			=> 1000,
); 

echo json_encode($info); 

/*
inboxinfo = new dkMusic.Models.InboxInfo({
	inbox_count 		: {{dkFolder::count_files_in(dkmusic_inbox)}},
	convert_count 		: {{dkFolder::count_files_in(dkmusic_internal_convert)}},
	missingdata_count 	: {{dkFolder::count_files_in(dkmusic_internal_missingdata)}},
	duplicates_count 	: {{dkFolder::count_files_in(dkmusic_output_duplicates)}},
	trash_count 		: {{dkFolder::count_files_in(dkmusic_trash)}},
});
*/

?>