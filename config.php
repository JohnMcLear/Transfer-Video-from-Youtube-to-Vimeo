<?php
/*  Config -- EDIT THESE SETTINGS!
    ------*/

$test_only = true; // should we only do a test run and not actually copy/paste anything?  A test run will output the stages but wont download or create any files on vimeo.
$youtube_username = 'mehtest'; // your youtube username
$vimeo_consumer_key = ''; // your vimeo consumer key
$vimeo_secret = ''; // your secret from vimeo
$logpath = "/var/log/youtube_to_vimeo.txt";

// DO NOT EDIT BELOW THIS LINE
$settings["test_only"] = $test_only;
$settings["youtube_username"] = $youtube_username;
$settings["vimeo_consumer_key"] = $vimeo_consumer_key;
$settings["vimeo_secret"] = $vimeo_secret;
$settings["logpath"] = $logpath;
?>
