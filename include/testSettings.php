function testSettings($settings){
  if($settings["test_only"] !== true || $settings["test_only"] !== false){
    die("test_only Setting is NOT SET");
  }
  $settings["youtube_username"] = $youtube_username;
  if($settings["youtube_username"] = "" || $settings["youtube_username"] = "meh"){
    die("youtube_username Setting is NOT SET");
  }
  $settings["vimeo_consumer_key"] = $vimeo_consumer_key;
  if($settings["vimeo_consumer_key"] = ""){
    die("vimeo_consumer_key Setting is NOT SET");
  }
  $settings["vimeo_secret"] = $vimeo_secret;
  if($settings["vimeo_secret"] = ""){
    die("vimeo_secret Setting is NOT SET");
  }
}

