<?php

require('assets/php/functions.php');

if(isset($_GET['theme'])) {
	$theme = $_GET['theme'];
} else {
	$theme = "default";
}

$username = substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'], '.png')-1);
if($username == '') {
	die('ERROR: bad REQUEST_URI');
}

/////////////////////////////////////////////////////////////////

// Needed for alternative file_get_contents
function url_get_contents ($Url) {
    if (!function_exists('curl_init')){ 
        die('CURL is not installed!');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// Get info and process it
$unprocessed = url_get_contents('https://keybase.io/_/api/1.0/user/lookup.json?usernames='.$username);
$processed = json_decode($unprocessed);

// do they exist?
if($processed->them[0] == NULL) {
	die('ERROR: This person doesn\'t exist');
}

$url_to_avatar = $processed->them[0]->pictures->primary->url;
$fingerprint_64bit = strtoupper(chunk_split(substr(($processed->them[0]->public_keys->primary->key_fingerprint), -16), 4, ' '));

$number_devices = count(get_object_vars($processed->them[0]->devices));
$twitter_proof = property_exists($processed->them[0]->proofs_summary->by_proof_type, "twitter");
$github_proof = property_exists($processed->them[0]->proofs_summary->by_proof_type, "github");
$reddit_proof = property_exists($processed->them[0]->proofs_summary->by_proof_type, "reddit");
$website_proof = property_exists($processed->them[0]->proofs_summary->by_proof_type, "dns");
$coinbase_proof = property_exists($processed->them[0]->proofs_summary->by_proof_type, "coinbase");
$hackernews_proof = property_exists($processed->them[0]->proofs_summary->by_proof_type, "hackernews");
$facebook_proof = property_exists($processed->them[0]->proofs_summary->by_proof_type, "facebook");
$bitcoin_aviable = property_exists($processed->them[0]->cryptocurrency_addresses, "bitcoin");
$zcash_aviable = property_exists($processed->them[0]->cryptocurrency_addresses, "zcash");

///////////////////////////////////////////////////////////////////

//setting the image header in order to proper display the image
header("Content-Type: image/png");
//and allow cors
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

// Create the image
$canvas = imagecreatetruecolor(420, 116);

//Set background color
switch ($theme) {
	case 'clean':
		$backgroundColor = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
		break;
	
	case 'dark':
		$backgroundColor = imagecolorallocate($canvas, 0, 0, 0);
		break;
	default:
		$backgroundColor = imagecolorallocate($canvas, 238, 238, 238);
		break;
}
imagefill($canvas, 0, 0, $backgroundColor);

//computed colors
$black = imagecolorallocate($canvas, 0, 0, 0);
$white = imagecolorallocate($canvas, 255, 255, 255);
$blue = imagecolorallocate($canvas, 51, 160, 255);
$orange = imagecolorallocate($canvas, 255, 111, 33);
$silver = imagecolorallocate($canvas, 192, 192, 192);

/////////////////////////////////////////////////////////////

//add avatar from keybase.io
if (substr($url_to_avatar, -3) == 'png') {
	$user_avatar = imagecreatefrompng($url_to_avatar);
} else {
	$user_avatar = imagecreatefromjpeg($url_to_avatar);
}
$scaled_user_avatar = imagescale($user_avatar, 100);
imagecopy($canvas, $scaled_user_avatar, 8, 8, 0, 0, 100, 100);


//If a username is long, change the look of the card
if(strlen($username) < 12) {

	//add "keybase.io/""
	if ($theme == 'dark') {
		imagettftext($canvas, 21, 0, 114, 36, $white, 'assets/fonts/Lato-Regular.ttf', 'keybase.io/');
	} else {
		imagettftext($canvas, 21, 0, 114, 36, $black, 'assets/fonts/Lato-Regular.ttf', 'keybase.io/');
	}

	//now add the username
	imagettftext($canvas, 21, 0, 256, 36, $orange, 'assets/fonts/Lato-Bold.ttf', $username);

} else {

	//add "keybase.io/""
	if ($theme == 'dark') {
		imagettftext($canvas, 19, 0, 114, 32, $white, 'assets/fonts/Lato-Regular.ttf', 'keybase.io/');
	} else {
		imagettftext($canvas, 19, 0, 114, 32, $black, 'assets/fonts/Lato-Regular.ttf', 'keybase.io/');
	}

	//now add the username
	imagettftext($canvas, 18, 0, 240, 32, $orange, 'assets/fonts/Lato-Bold.ttf', $username);

}

//now add the 64 bit fingerprint
imagettftext($canvas, 18, 0, 140, 66, $blue, 'assets/fonts/Lato-Regular.ttf', $fingerprint_64bit);

//add the key before the fingerprint
if ($theme == 'dark') {
	$key_icon = imagecreatefrompng('assets/icons/dark/key-icon.png');
} else {
	$key_icon = imagecreatefrompng('assets/icons/default/vintage-key-outline.png');
}
imagecopyresampled($canvas, $key_icon, 114, 48, 0, 0, 20, 20, 50, 50);

////////////////////////////////////////////////////////////////////////////////////////////////////

//set initial x position, increase by 15
$proof_x_position = 114;

//add # of devices w/ icon
if($number_devices) {
	if ($theme == 'dark') {
		imagettftext($canvas, 20, 0, 114, 100, $white, 'assets/fonts/Lato-Regular.ttf', $number_devices);
		$device_icon = imagecreatefrompng('assets/icons/dark/mobile-icon.png');
	} else {
		imagettftext($canvas, 20, 0, 114, 100, $black, 'assets/fonts/Lato-Regular.ttf', $number_devices);
		$device_icon = imagecreatefrompng('assets/icons/default/mobile-phone.png');
	}
	if ($number_devices <= 9) {
		imagecopyresampled($canvas, $device_icon, 130, 80, 0, 0, 20, 20, 50, 50);
		$proof_x_position = 150;
	} else {
		imagecopyresampled($canvas, $device_icon, 150, 80, 0, 0, 20, 20, 50, 50);
		$proof_x_position = 170;
	}

}

//add twitter
if ($twitter_proof) {
	$twitter_icon = imagecreatefrompng('assets/icons/default/rsz_twitter-black-shape.png');
	imagecopyresampled($canvas, $twitter_icon, $proof_x_position, 80, 0, 0, 20, 20, 50, 50);
	$proof_x_position = $proof_x_position + 25;
}

//add github
if ($github_proof) {
	if ($theme == 'dark') {
		$github_icon = imagecreatefrompng('assets/icons/dark/github-icon.png');
	} else {
		$github_icon = imagecreatefrompng('assets/icons/default/github-logo.png');
	}
	imagecopyresampled($canvas, $github_icon, $proof_x_position, 80, 0, 0, 20, 20, 50, 50);
	$proof_x_position = $proof_x_position + 25;
}

//add reddit
if($reddit_proof) {
	$reddit_icon = imagecreatefrompng('assets/icons/default/reddit-big-logo.png');
	imagecopyresampled($canvas, $reddit_icon, $proof_x_position, 80, 0, 0, 20, 20, 50, 50);
	$proof_x_position = $proof_x_position + 25;
}

//add coinbase
if($coinbase_proof){
	$coinbase_icon = imagecreatefrompng('assets/icons/default/coinbase.png');
	imagecopyresampled($canvas, $coinbase_icon, $proof_x_position, 80, 0, 0, 20, 20, 50, 50);
	$proof_x_position = $proof_x_position + 25;
}

//add hacker news
if($hackernews_proof) {
	$hacker_news_icon = imagecreatefrompng('assets/icons/default/hacker-news.png');
	imagecopyresampled($canvas, $hacker_news_icon, $proof_x_position, 80, 0, 0, 20, 20, 50, 50);
	$proof_x_position = $proof_x_position + 25;
}

//add facebook
if($facebook_proof) {
	$facebook_icon = imagecreatefrompng('assets/icons/default/facebook.png');
	imagecopyresampled($canvas, $facebook_icon, $proof_x_position, 80, 0, 0, 20, 20, 50, 50);
	$proof_x_position = $proof_x_position + 25;
}

//add globe (websites)
if($website_proof) {
	$globe_icon = imagecreatefrompng('assets/icons/default/globe.png');
	imagecopyresampled($canvas, $globe_icon, $proof_x_position, 80, 0, 0, 20, 20, 50, 50);
	$proof_x_position = $proof_x_position + 25;
}

//add bitcoin
if($bitcoin_aviable) {
	$bitcoin_icon = imagecreatefrompng('assets/icons/default/bitcoin.png');
	imagecopyresampled($canvas, $bitcoin_icon, $proof_x_position, 80, 0, 0, 20, 20, 50, 50);
	$proof_x_position = $proof_x_position + 25;
}

//add zcash
if ($zcash_aviable) {
	if ($theme == 'dark') {
		$zcash_icon = imagecreatefrompng('assets/icons/dark/zcash-icon.png');
	} else {
		$zcash_icon = imagecreatefrompng('assets/icons/default/zcash-icon.png');
	}
	imagecopyresampled($canvas, $zcash_icon, $proof_x_position, 80, 0, 0, 20, 20, 50, 50);
	$proof_x_position = $proof_x_position + 25;
}

/////////////////////////////////////////////////////////////////////////////////////////////

//add a border
if ($theme == 'default') {
	drawBorder($canvas, $silver);
}

//outputs the image as png
imagesavealpha($canvas, true);
imagepng($canvas);

//frees any memory associated with the image 
imagedestroy($canvas);
