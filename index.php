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
require_once('include/curlFunctions.php');
require_once('include/youtubeFunctions.php');
require_once('include/testSettings.php');

// Debug message..
if ($test_only == true)
{
echo "Running in debug only mode";
echo "videos will NOT be downloaded from youtube or uploaded to Vimeo but you will get a nice output..";
}

// Testing settings
testSettings($settings);

/*************************************************************************************************
Execute Step 1. Query your YouTube account.
Gets a list of all videos then executes all other functions in a beautifully blocking way..
**************************************************************************************************/
$videos = get_youtube_videos($youtube_username, $test_only, 1);

?>
