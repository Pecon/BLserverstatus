<?php
//Originally by Zack0wack0, savaged and rehosted by Kalphiter; now re-rehosted and maintained by Pecon

if(!isSet($_GET["h"]) && !isSet($_GET["a"]))
{
	header("Location: /linkGenerator.html");
	return;
}

if(!isSet($_GET['t']) || $_GET['t'] > 5 && $_GET['t'] < 2)
	$imageTemplate = 3;
else
	$imageTemplate = $_GET['t'];

if(!isSet($_GET['i']) || ($_GET['i'] != "png" && $_GET['i'] != "gif" && $_GET['i'] != "jpeg"))
	$imageType = "png";
else
	$imageType = $_GET['i'];

$loadedTime = time();
if(file_exists("server-status_cache_details.txt") && file_exists("server-status_cache.txt"))
{
	$cacheTime = intval(file_get_contents("server-status_cache_details.txt"));
	if($loadedTime - $cacheTime < 60)
		$servers = file("server-status_cache.txt");
}

if(!isSet($servers))
{
	ini_set('default_socket_timeout', 5);
	$servers = file("http://master2.blockland.us/");

	// Try to verify that the list was served correctly so we don't make an image with a bunch of junk data.
	$fail = false;
	
	if(!isSet($servers[1]))
		$fail = true;
	else if(trim($servers[1]) != "START")
		$fail = true;
	else if(trim($servers[count($servers) - 2]) != "END")
		$fail = true;

	if($fail)
	{
		// Report this error
		$file = fopen("./masterservererror.log", 'a');
		$file.fwrite("\n\n==================================\n" . date() . "\n" . $servers);

		// Okay, let's see how old the cache is. If it's new enough we can just serve that.
		if(isSet($cacheTime))
		{
			$recovered = false;
			if($loadedTime - $cacheTime < 240)
			{
				$servers = file("server-status_cache.txt");
				$recovered = true;
			}
		}

		if(!$recovered)
		{
			// The master server has an unusually high rate of returning blank pages. Probably a bug on it's end, nothing we can really do about that.
			// Try waiting a second and loading again before giving up.

			sleep(1000);

			$servers = file("http://master2.blockland.us/");
			$fail = false;

			if(trim($servers[1]) != "START")
				$fail = true;

			else if(trim($servers[count($servers) - 2]) != "END")
				$fail = true;

			if($fail)
			{
				header("Content-type: image/PNG");
				readfile("./images/unavailable.png");
				exit();
			}
		}
	}
	else
	{
		file_put_contents("server-status_cache_details.txt", $loadedTime);
		file_put_contents("server-status_cache.txt", implode("", $servers));
	}
}

//Figure out which server on the list matches the requested one.
$target = false;
foreach($servers as $index => $server)
{
	$server = explode("\t", $server);

	if(count($server) == 1)
		continue;

	$host = strpos($server[4], "'", 0);
	$host = substr($server[4], 0, $host);

	if(isSet($_GET['a']))
	{
		if(isSet($_GET["p"]) && $server[1] != $_GET["p"])
			continue;

		if($server[0] == $_GET["a"])
		{
			$target = $server;
			break;
		}
	}
	else if(isSet($_GET['h']))
	{
		if($host == $_GET["h"])
		{
			$target = $server;
			break;
		}
	}
	else
		break;
}

// Set up text to write to the images
if(count($target) > 1)
{
	$name = $target[4];

	$dedi = ($target[3] == 1 ? "Yes" : "No");
	$passed = ($target[2] == 1 ? "Yes" : "No");
	$players = $target[5] . "/" . $target[6];
	$bricks = $target[8];
	$gamemode = $target[7];
}
else
{
	$name = isSet($_GET["h"]) ? $_GET["h"] . "'s Server" : $_GET["a"];
	if(isSet($_GET["n"]))
		$name = $_GET["n"];

	$dedi = "N/A";
	$passed = "N/A";
	$players = "N/A";
	$bricks = "N/A";
	$gamemode = "N/A";
}

// Make the image and write the text.
switch($imageTemplate)
{
	case 2:
		$im = imagecreatefrompng("./images/template2.png");
		$font = "images/segoeUI.ttf";
		imagesavealpha($im, true);

		// Choose colors
		$red = imagecolorallocate($im, 220, 0, 0);
		$purple = imagecolorallocate($im, 220, 0, 220);
		$green = imagecolorallocate($im, 20, 220, 20);
		$white = imagecolorallocate($im, 255, 255, 255);
		$black = imagecolorallocate($im, 15, 15, 15);

		if($target[5] >= $target[6])
			$titleColor = $red;
		else if($target[2])
			$titleColor = $purple;
		else if($target[5] < $target[6])
			$titleColor = $green;
		else
			$titleColor = $white;

		imagettftext($im, 12, 0, 13, 36, $titleColor, $font, $name);
		imagettftext($im, 10, 0, 80, 65, $black, $font, $dedi);
		imagettftext($im, 10, 0, 94, 97, $black, $font, $passed);
		imagettftext($im, 10, 0, 64, 132, $black, $font, $players);
		imagettftext($im, 10, 0, 59, 165, $black, $font, $bricks);
		imagettftext($im, 10, 0, 88, 199, $black, $font, $gamemode);

		break;

	case 3:
		$im = imagecreatefrompng("./images/template3.png");
		$font = "images/segoeUI.ttf";
		imagesavealpha($im, true);

		// Choose colors
		$red = imagecolorallocate($im, 220, 0, 0);
		$purple = imagecolorallocate($im, 220, 0, 220);
		$green = imagecolorallocate($im, 20, 220, 20);
		$white = imagecolorallocate($im, 255, 255, 255);
		$black = imagecolorallocate($im, 15, 15, 15);

		if($target[5] >= $target[6])
			$titleColor = $red;
		else if($target[2])
			$titleColor = $purple;
		else if($target[5] < $target[6])
			$titleColor = $green;
		else
			$titleColor = $white;

		imagettftext($im, 12, 0, 10, 18, $titleColor, $font, $name);
		imagettftext($im, 10, 0, 30, 42, $white, $font, $players);
		imagettftext($im, 10, 0, 100, 42, $white, $font, $passed);
		imagettftext($im, 10, 0, 157, 42, $white, $font, $dedi);
		imagettftext($im, 10, 0, 212, 42, $white, $font, $bricks);
		imagettftext($im, 10, 0, 295, 42, $white, $font, $gamemode);

		break;

	case 4:
		$im = imagecreatefrompng("./images/template4.png");

		$fontReg = "./images/rakesly.ttf";
		$fontBold = "./images/rakeslyb.ttf";
		imagesavealpha($im, true);

		// Choose colors
		$lightGrey = imagecolorallocate($im, 65, 65, 65);
		$darkGrey = imagecolorallocate($im, 45, 45, 45);

		$dedi = "Dedicated: " . $dedi;
		$passed = "Passworded: " . $passed;
		$players = "Players: " . $players;
		$bricks = "Bricks: " . $bricks;
		$gamemode = "Gamemode: " . $gamemode;

		imagettftext($im, 14, 0, 3, 21, $darkGrey, $fontBold, $name);
		imagettftext($im, 15, 0, 10, 52, $lightGrey, $fontReg, $dedi);
		imagettftext($im, 15, 0, 10, 88, $lightGrey, $fontReg, $passed);
		imagettftext($im, 15, 0, 10, 125, $lightGrey, $fontReg, $players);
		imagettftext($im, 15, 0, 10, 162, $lightGrey, $fontReg, $bricks);
		imagettftext($im, 15, 0, 10, 199, $lightGrey, $fontReg, $gamemode);

		break;

	case 5:
		$im = imagecreatefrompng("./images/template5.png");
		$fontReg = "./images/rakesly.ttf";
		$fontBold = "./images/rakeslyb.ttf";
		imagesavealpha($im, true);

		// Choose colors
		$lightGrey = imagecolorallocate($im, 65, 65, 65);
		$darkGrey = imagecolorallocate($im, 45, 45, 45);

		imagettftext($im, 14, 0, 3, 21, $lightGrey, $fontBold, $name);
		imagettftext($im, 15, 0, 26, 47, $lightGrey, $fontReg, $players);
		imagettftext($im, 15, 0, 89, 47, $lightGrey, $fontReg, $passed);
		imagettftext($im, 15, 0, 151, 47, $lightGrey, $fontReg, $dedi);
		imagettftext($im, 15, 0, 215, 47, $lightGrey, $fontReg, $bricks);
		imagettftext($im, 15, 0, 282, 47, $lightGrey, $fontReg, $gamemode);

		break;
}

if($im)
{
	header("Content-type: image/" . $imageType);
	switch($imageType)
	{
		case "png":
			imagepng($im);
			break;
		case "gif":
			imagegif($im);
			break;
		case "jpeg":
			imagejpeg($im);
			break;
	}
	imagedestroy($im);
}
?>
