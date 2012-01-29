<?php
require_once('../config.php');
require_once('vimeo.php');
session_start();

// Create the object and enable caching
$vimeo = new phpVimeo($vimeo_consumer_key, $vimeo_secret);
$vimeo->enableCache(phpVimeo::CACHE_FILE, './cache', 300);

// Clear session
if ($_GET['clear'] == 'all') {
    session_destroy();
    session_start();
}

// Set up variables
$state = $_SESSION['vimeo_state'];
$request_token = $_SESSION['oauth_request_token'];
$access_token = $_SESSION['oauth_access_token'];
$file = $_GET['file'];
$file = str_ireplace(" ","+",$file);
$_SESSION['file'] = $file;
$file = $_SESSION['file'];
$title = $_GET['title'];
$title = str_ireplace("+"," ",$title);
$_SESSION['title'] = $title;
$title = $_SESSION['title'];
$description = $_GET['description'];
$description = str_ireplace("&"," ",$description);
$description = str_ireplace("+"," ",$description);
$_SESSION['description'] = $description;
$description = $_SESSION['description'];


// Coming back
if ($_REQUEST['oauth_token'] != NULL && $_SESSION['vimeo_state'] === 'start') {
    $_SESSION['vimeo_state'] = $state = 'returned';
}

// If we have an access token, set it
if ($_SESSION['oauth_access_token'] != null) {
    $vimeo->setToken($_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);
    $_SESSION['file'] = $_GET['file'];
}

switch ($_SESSION['vimeo_state']) {
    default:

        // Get a new request token
        $token = $vimeo->getRequestToken();

        // Store it in the session
        $_SESSION['oauth_request_token'] = $token['oauth_token'];
        $_SESSION['oauth_request_token_secret'] = $token['oauth_token_secret'];
        $_SESSION['vimeo_state'] = 'start';

        // Build authorize link
        $authorize_link = $vimeo->getAuthorizeUrl($token['oauth_token'], 'write');

        break;

    case 'returned':

        // Store it
        if ($_SESSION['oauth_access_token'] === NULL && $_SESSION['oauth_access_token_secret'] === NULL) {
            // Exchange for an access token
            $vimeo->setToken($_SESSION['oauth_request_token'], $_SESSION['oauth_request_token_secret']);
            $token = $vimeo->getAccessToken($_REQUEST['oauth_verifier']);

            // Store
            $_SESSION['oauth_access_token'] = $token['oauth_token'];
            $_SESSION['oauth_access_token_secret'] = $token['oauth_token_secret'];
            $_SESSION['vimeo_state'] = 'done';

            // Set the token
            $vimeo->setToken($_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);
        }

        // Do an authenticated call
        try {
            $videos = $vimeo->call('vimeo.videos.getUploaded');
        }
        catch (VimeoAPIException $e) {
            echo "Encountered an API error -- code {$e->getCode()} - {$e->getMessage()}";
        }

        break;
}

try {
    echo "Trying to upload $file\n";
    $video_id = $vimeo->upload($file);
    if ($video_id) {
        echo '<a href="http://vimeo.com/' . $video_id . '">Upload successful!</a>';

        //$vimeo->call('vimeo.videos.setPrivacy', array('privacy' => 'nobody', 'video_id' => $video_id));
        $vimeo->call('vimeo.videos.setTitle', array('title' => $title, 'video_id' => $video_id));
        $vimeo->call('vimeo.videos.setDescription', array('description' => $description, 'video_id' => $video_id));
    }
    else {
        echo "Video file did not exist!";
    }
}
catch (VimeoAPIException $e) {
    echo "Encountered an API error -- code {$e->getCode()} - {$e->getMessage()}";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Vimeo Advanced API OAuth Example</title>
</head>
<body>

    <?php if ($_SESSION['vimeo_state'] == 'start'): ?>
        <p>Click the link to go to Vimeo to authorize your account.</p>
        <p><a href="<?= $authorize_link ?>"><?php echo $authorize_link ?></a></p>
    <?php endif ?>

    <?php if ($ticket): ?>
        <pre><?php // print_r($ticket) ?></pre>
    <?php endif ?>

    <?php if ($videos): ?>
        <pre><?php // print_r($videos) ?></pre>
    <?php endif ?>

<a href="?clear=all">click here if you want to start over w/ vimeo api</a>.</p>

</body>
</html>
