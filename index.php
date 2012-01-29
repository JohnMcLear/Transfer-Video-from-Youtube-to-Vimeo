<?php
require_once('config.php');
require_once('include/curlFunctions.php');
require_once('include/youtubeFunctions.php');
require_once('include/testSettings.php');

// Testing settings
testSettings($settings);

/*************************************************************************************************
Execute Step 1. Query your YouTube account.
Gets a list of all videos then executes all other functions in a beautifully blocking way..
**************************************************************************************************/
$videos = get_youtube_videos($youtube_username, $test_only, 1);

?>
