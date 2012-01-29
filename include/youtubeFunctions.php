<?php
// Function for Step 1 & 2 Query your YouTube account, gets a list of all videos
function get_youtube_videos($youtube_username, $test_only, $startindex)
{
  $i = 0; // count of the # of videos
  $url = 'http://gdata.youtube.com/feeds/api/users/'.$youtube_username.'/uploads?start-index='.$startindex.'&max-results=50';
  $videos = simplexml_load_file($url);
  // we may aswell get the metadata here..
  $counts = $videos->children('http://a9.com/-/spec/opensearchrss/1.0/');
  $count = 0;
  foreach ($videos->entry as $video) {
    $count++;
    $media = $video->children('http://search.yahoo.com/mrss/');
    $attrs = $media->group->player->attributes();
    $name = $media->group->title;
    $description = $media->group->description;
    $url = $attrs['url'];
    $url = str_ireplace("&feature=youtube_gdata_player","",$url);
    $metadata = array($name, $description, $url);
    // Debug output
    if ($testonly != true){
      echo "\n\n<div class='video'>";
      echo "<br/>Found Video #$i --->";
      echo "<br/>Name: $name";
      echo "<br/>Description: $description";
      echo "<br/>URL: $url<br/>";
    }
    // Execute Step 3. Get the video file for this Video
/*    while ($count < 10){
      // only do 10 at a time due to youtube expring urls
*/
      $video_data = get_youtube_video($metadata, $test_only);
      $count++;
/*
    }
*/
    // Execute Step 4. Upload the video file and metadata to Vimeo via the API
    push_youtube_video_and_meta($metadata, $video_data, $test_only);
    //cake remove the below line
    $i++;
  }
}

// Function for Step 3. Get the video file for this Video
function get_youtube_video($metadata, $test_only)
{
 // Debug output
  include_once('include/curl.php');
  include_once('include/youtube.php');
  $tube = new youtube();
  // get all of the possible video qualities for this video
  $links = $tube->get($metadata[2]);
  if($links)
    {
    foreach ($links as $link)
      {
      // try get a 720p video
      if (strpos($link[type],"720"))
      {
      $link = $link[url];
      }
      // if there is no 720p video then settle for 480
      elseif (strpos($link[type],"480"))
      {
      $link = $link[url];
      }
    }
  echo "Considering Grabbing Video file from: $link<br/><br/>";
  }
  if ($test_only == false)
  {
    $filename = urlencode($metadata[0]);
    $filename = str_ireplace(".","",$filename);
    $filepath = "/tmp/$filename.mpg";
    // if the file doesn't already exist then download it
    $id = $filename;
    $id = str_ireplace("+","",$id);
    if (!file_exists($filepath)){
    echo "Downloading video to $filepath .......  <b>Please be patient</b><br/>";
      $returned_content = get_data($link);
      $fh = fopen($filepath, 'w');
        if ($returned_content){
          fwrite($fh, $returned_content);
        }
        else
        {
        echo "<br/>WARNING: Get of $filename failed!";
        }
      echo "\n<div class='uploadbutton' id='$id'><a onClick='\$(\"#$id\").html(\"Wait...\");$.get(\"vimeo/authenticate.php\", {file: \"$filepath\", title: \"$metadata[0]\", description: \"$metadata[1]\"}, function(data){\$(\"#$id\").html(data);});return false;' href=\"vimeo/authenticate.php?file=$filepath&title=$metadata[0]&description=$metadata[1]\">Upload me</a><br/><br/></div>";
    }
  else
    {
      echo "\n<div class='uploadbutton' id='$id'><a onClick='\$(\"#$id\").html(\"Wait...\");$.get(\"vimeo/authenticate.php\", {file: \"$filepath\", title: \"$metadata[0]\", description: \"$metadata[1]\"}, function(data){\$(\"#$id\").html(data);});return false;' href=\"vimeo/authenticate.php?file=$filepath&title=$metadata[0]&description=$metadata[1]\">File already exists!  Woohoo!  Upload me</a><br/><br/></div>";
    }
  }
  else
  {
    echo "Test mode currently enabled, file upload not supported";
  }
  echo "</div>\n";
}


// Function for Step 4. Upload the video file and metadata to Vimeo via the API
function push_youtube_video_and_meta($metadata, $video_data, $test_only)
{
print_r($video_data);
}
?>
