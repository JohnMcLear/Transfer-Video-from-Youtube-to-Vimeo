<?php
function testSettings($settings){
  if($settings["test_only"] !== true && $settings["test_only"] !== false){
    error_log("test_only Setting is NOT SET", 3, $settings["logpath"]);
    die("test_only Setting is NOT SET");
  }
  if($settings["youtube_username"] = "" || $settings["youtube_username"] = "meh"){
    error_log("youtube_username Setting is NOT SET", 3, $settings["logpath"]);
    die("youtube_username Setting is NOT SET");
  }
  if($settings["vimeo_consumer_key"] = ""){
    error_log("vimeo_consumer_key Setting is NOT SET", 3, $settings["logpath"]);
    die("vimeo_consumer_key Setting is NOT SET");
  }
  if($settings["vimeo_secret"] = ""){
    error_log("vimeo_secret Setting is NOT SET", 3, $settings["logpath"]);
    die("vimeo_secret Setting is NOT SET");
  }

  // Debug message..
  if ($settings["test_only"] === true)
  {
  stdout("Running in debug only mode");
  echo "videos will NOT be downloaded from youtube or uploaded to Vimeo but you will get a nice output..";
  }
}
?>
