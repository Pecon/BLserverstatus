<?php
//Originally by Zack0wack0, savaged and rehosted by Kalphiter; now re-rehosted and maintained by Pecon
ob_start();
error_reporting(E_ALL);

function getFreshServerList()
{
	ini_set("default_socket_timeout", 5);
	$list = file_get_contents("http://master3.blockland.us/");

	$entries = explode("\n", $list);
	$serverList = Array();

	foreach($entries as $entry)
	{
		$entry = explode("\t", trim($entry));

		if(count($entry) < 2)
			continue; // Bad entry

		if(is_numeric($entry[1]))
		{
			// It's probably good.
			$server = Array();
			$server['ip'] = $entry[0];
			$server['port'] = $entry[1];
			$server['passworded'] = $entry[2];
			$server['dedicated'] = $entry[3];
			$server['servername'] = $entry[4];
			$server['players'] = $entry[5];
			$server['maxplayers'] = $entry[6];
			$server['gamemode'] = $entry[7];
			$server['brickcount'] = $entry[8];
			$server['blid'] = $entry[9];
			$server['adminname'] = $entry[10];
			$server['steamid'] = $entry[11];

			array_push($serverList, $server);
		}
	}

	if(count($serverList) < 1)
		return false;

	return $serverList;
}

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

$cacheMTime = 0;
$saveCache = false;
if(is_file("./cache.dat"))
{
	$serverList = file_get_contents("./cache.dat");
	$serverList = unserialize($serverList);

	if($serverList === false)
		unset($serverList);

	$cacheMTime = filemtime("./cache.dat");
}

// Cache is bad.
if(!isset($serverList))
{
	$serverList = Array();
	$serverList['servers'] = getFreshServerList();
	$serverList['time'] = time();
	$saveCache = true;

	if($serverList === false)
		exit(file_get_contents("./images/unavailable.png"));
}
else if($serverList['time'] < time() - 30) // Cache is old
{
	$newList = getFreshServerList();

	if($newList !== false)
	{
		$serverList = Array();
		$serverList['servers'] = $newList;
		$serverList['time'] = time();
		$saveCache = true;
	}
}

// Save new cache
if($saveCache)
{
	$data = serialize($serverList);

	if(filemtime("./cache.dat") == $cacheMTime)
	{
		file_put_contents("./cache.dat", $data);
	}
	else
	{
		// Race condition caught: The file was modified since we initially read the cache. Don't save over it now.
	}
}

// Find target server
if(isset($_GET['h']))
{
	foreach($serverList['servers'] as $server)
	{
		if(strtolower($server['adminname']) == strtolower(trim($_GET['h'])))
		{
			$target = $server;
			break;
		}
	}
}

if(!isset($target) && isset($_GET['a']))
{
	foreach($serverList['servers'] as $server)
	{
		if($server['ip'] == trim($_GET['a']))
		{
			if(isset($_GET['p']))
			{
				if(strlen($_GET['p']) > 1 && $_GET['p'] != $server['port'])
					continue; // Not the specified port
			}

			$target = $server;
			break;
		}
	}
}

// Set up text to write to the images
if(isset($target))
{
	$host = $target['adminname'];
	$name = $host . (substr($host, strlen($host) - 2) == 's' ? "'" : "'s") . " " . $target['servername'];

	$dedi = ($target['dedicated'] == 1 ? "Yes" : "No");
	$passed = ($target['passworded'] == 1 ? "Yes" : "No");
	$players = $target['players'] . "/" . $target['maxplayers'];
	$bricks = $target['brickcount'];
	$gamemode = $target['gamemode'];
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

	$target = Array('players' => 0, 'maxplayers' => 0, 'dedicated' => 0, 'passworded' => 0, 'brickcount' => 0, 'gamemode' => null, 'adminname' => null, 'servername' => null);
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

		if($target['players'] >= $target['maxplayers'])
			$titleColor = $red;
		else if($target['passworded'])
			$titleColor = $purple;
		else if($target['players'] < $target['maxplayers'])
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

		if($target['players'] >= $target['maxplayers'])
			$titleColor = $red;
		else if($target['passworded'])
			$titleColor = $purple;
		else if($target['players'] < $target['maxplayers'])
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

$errors = ob_get_contents();
if(strlen($errors) > 2)
{
	$handle = fopen("./error.log", 'a');
	fwrite($handle, $errors);
	fclose($handle);
}
ob_end_clean();

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
