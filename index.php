<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
<style type="text/css">
.video{
  video: height:80px;
  border-style:solid;
  border-width:2px;
  position:relative;
  margin-bottom:10px;
}
.uploadbutton
{
  background:red;
  position:absolute;
  right:0px;
  top:0px;
  width:200px;
  height:200px;
}
</style>

<?php
/*  Hello Dave. --> Read this or skip to config.  Either way you MUST edit config.php
    -----------

This script will copy your youtube videos and their metadata to vimeo.  That is all.
This script is built on various other scripts, mostly ngcoders.com -- I don't think Google or anyone else will like it but it's useful and it's open source.  Use it well, don't steal content.  Be nice.  Don't be a tool.  This script is released under the GPL crazy noodles afero hombre license, to use it you must supply the author with tapas.  The author is your mouth.  You must eat tapas.  */

/*  Prereq's & installation  -- READ THIS it wont work w/ out making changes you tard.
    ----------
1. php5 & curl..
2. php.ini for Apache w/ a large memory setting. edit memory setting in /etc/php5/apache2/php.ini or whatever
3. ssh/shell access (im too lazy to write a web front end)..
4. Your videos must have a 720p download available else this is just a waste of everyones times.  Let's make video on the web better ey?
5. You get a Vimeo API key.  http://vimeo.com/api/applications/new  -- When registering you will need to set it as a Write application and set a Callback URL as FullURL/vimeo/authenticate.php -- 
You also want to request perms to upload via  -- This can take up to a few days.  After registration is click click Upload Access - request, then copy your consumer key and secret to the below options
6. Visit a URL where you extracted the files.

/* How it works...
Step 1. Query your YouTube account, gets a list of all videos
Step 2. For each video get the ID, metadata (name, description etc.) and store that in an array
Step 3. Get the video file for this Video
Step 4. Upload the video file and metadata to Vimeo via the API
Step 5. Return to Step 2, it's a funking loopsi. */

/* Limitations, bceause y'know I'm hella lazy.
1. Unicorn rainbows are limited to 127 per session. 
2. You can't exclude certain videos
3. You can only do 50 videos at a time. // run it again w/ a higher value in the start-index if you want to do more than 50.
4. First upload fails right now, just a bug..
5. Breaks upload on many characters, just requires some encoding
*/

require_once('config.php');
/* End of config - Here be camels. Edit below this line at the risk of being violated by camels */

// BEGIN OF FUNCTIONS
//////////////////////////////////////////

// A curl function
function get_data($url)
{
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

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
  include_once('curl.php');
  include_once('youtube.php');
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

// END OF FUNCTIONS
//////////////////////////////////////////

// Debug message..
if ($test_only == true)
{
echo "Running in debug only mode, no videos will be downloaded from youtube or uploaded to Vimeo but you will get a nice output..<br/>";
}

// Execute Step 1. Query your YouTube account, gets a list of all videos then executes all other functions in a beautifully blocking way..  
$videos = get_youtube_videos($youtube_username, $test_only, 1);

?>
