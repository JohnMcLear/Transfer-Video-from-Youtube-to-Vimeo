<?php
function testSettings($settings){
  if($settings["test_only"] !== true && $settings["test_only"] !== false){
    die("test_only Setting is NOT SET");
  }
  if($settings["youtube_username"] = "" || $settings["youtube_username"] = "meh"){
    die("youtube_username Setting is NOT SET");
  }
  if($settings["vimeo_consumer_key"] = ""){
    die("vimeo_consumer_key Setting is NOT SET");
  }
  if($settings["vimeo_secret"] = ""){
    die("vimeo_secret Setting is NOT SET");
  }
}
?>
