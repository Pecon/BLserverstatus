<?php//By Zack0wack0, savaged and rehosted by Kalphiter, and now re-rehosted by Pecon7error_reporting(1);if(count($_GET) <= 0 || (!isSet($_GET["h"]) && !isSet($_GET["a"]))){	header("Location: /linkGenerator.html");	// echo("<html><head><meta http-equiv=\"Refresh\" content=\"1; URL=./linkGenerator.html\"></head><body></body></html>");	return;}if(!isSet($_GET["t"]) || $_GET["t"] > 5 && $_GET["t"] < 2){	$_GET["t"] = 3;}if(!isSet($_GET["i"]) || ($_GET["i"] != "png" && $_GET["i"] != "gif" && $_GET["i"] != "jpeg")){	$_GET["i"] = "png";}$loadedTime = time();if(file_exists("server-status_cache_details.txt") && file_exists("server-status_cache.txt")){	$cacheTime = file_get_contents("server-status_cache_details.txt");	if($cacheTime != "" && is_numeric($cacheTime) && $loadedTime - $cacheTime < 60)		$servers = file("server-status_cache.txt");}if(!isSet($servers)){	$servers = file("http://master2.blockland.us/");	// Try to verify that the file downloaded	$fail = false;	if(trim($servers[1]) != "START")		$fail = true;	if(trim($servers[count($servers) - 2]) != "END")		$fail = true;	if($fail)	{		// Okay, let's see how old the cache is. If it's new enough we can just serve that.		if(file_exists("server-status_cache_details.txt") && file_exists("server-status_cache.txt"))		{			$recovered = false;			$cacheTime = file_get_contents("server-status_cache_details.txt");			if($cacheTime != "" && is_numeric($cacheTime) && $loadedTime - $cacheTime < 240)			{				$servers = file("server-status_cache.txt");				$recovered = true;			}		}		if(!$recovered)		{			header("Content-type: image/PNG");			readfile("./images/unavailable.png");			exit();		}	}	else	{		file_put_contents("server-status_cache_details.txt",$loadedTime);		file_put_contents("server-status_cache.txt",implode("",$servers));	}}foreach($servers as $index => $server){	$server = explode("\t",$server);	if(count($server) == 1)		continue;	$host = strpos($server[4],"'",0);	$host = substr($server[4],0,$host);	if(isSet($_GET["p"]) && $server[1] != $_GET["p"])		continue;	if($host == $_GET["h"])		$target = $server;	else if($server[0] == $_GET["a"])		$target = $server;	if(count($target) >= 9)		break;}switch($_GET['t']){	case 2:		if(count($target) > 1)		{			$im = imagecreatefrompng("./images/template2.png");			$red = imagecolorallocate($im,220,0,0);			$purple = imagecolorallocate($im,220,0,220);			$green = imagecolorallocate($im,20,220,20);			$white = imagecolorallocate($im,255,255,255);			$black = imagecolorallocate($im,15,15,15);			imagesavealpha($im,true);			$name = $target[4];			if(isSet($_GET[n]))				$name = $_GET[n];			$dedi = $target[3] == 1 ? "Yes" : "No";			$passed = $target[2] == 1 ? "Yes" : "No";			$players = $target[5] . "/" . $target[6];			$bricks = $target[8];			$map = $target[7];			if($target[5] >= $target[6])				imagettftext($im,12,0,13,36,$red,"./images/segoeUI.ttf",$name);			else if($target[2])				imagettftext($im,12,0,13,36,$purple,"./images/segoeUI.ttf",$name);			else if($target[5] < $target[6])				imagettftext($im,12,0,13,36,$green,"./images/segoeUI.ttf",$name);			else				imagettftext($im,12,0,13,36,$white,"./images/segoeUI.ttf",$name);			imagettftext($im,10,0,64,132,$black,"images/segoeUI.ttf",$players);			imagettftext($im,10,0,94,97,$black,"images/segoeUI.ttf",$passed);			imagettftext($im,10,0,80,65,$black,"images/segoeUI.ttf",$dedi);			imagettftext($im,10,0,59,165,$black,"images/segoeUI.ttf",$bricks);			imagettftext($im,10,0,88,199,$black,"images/segoeUI.ttf",$map);		}		else		{			$im = imagecreatefrompng("./images/template2.png");			$white = imagecolorallocate($im,255,255,255);			$black = imagecolorallocate($im,15,15,15);			imagesavealpha($im,true);			$name = isSet($_GET["h"]) ? $_GET["h"] . "'s Server" : $_GET["a"];			if(isSet($_GET["n"]))				$name = $_GET["n"];			$dedi = "N/A";			$passed = "N/A";			$players = "N/A";			$bricks = "N/A";			$map = "N/A";			imagettftext($im,12,0,13,36,$white,"images/segoeUI.ttf",$name);			imagettftext($im,10,0,64,132,$black,"images/segoeUI.ttf",$players);			imagettftext($im,10,0,94,97,$black,"images/segoeUI.ttf",$passed);			imagettftext($im,10,0,80,65,$black,"images/segoeUI.ttf",$dedi);			imagettftext($im,10,0,59,165,$black,"images/segoeUI.ttf",$bricks);			imagettftext($im,10,0,88,199,$black,"images/segoeUI.ttf",$map);		}		break;		case 3:		if(count($target) > 1)		{			$im = imagecreatefrompng("./images/template3.png");			$red = imagecolorallocate($im,255,0,0);			$purple = imagecolorallocate($im,255,0,255);			$green = imagecolorallocate($im,0,255,0);			$white = imagecolorallocate($im,255,255,255);			imagesavealpha($im,true);			$name = $target[4];			if(isSet($_GET[n]))				$name = $_GET[n];			$dedi = $target[3] == 1 ? "Yes" : "No";			$passed = $target[2] == 1 ? "Yes" : "No";			$players = $target[5] . "/" . $target[6];			$bricks = $target[8];			$map = $target[7];			if($target[5] >= $target[6])				imagettftext($im,12,0,10,18,$red,"./images/segoeUI.ttf",$name);			else if($target[2])				imagettftext($im,12,0,10,18,$purple,"./images/segoeUI.ttf",$name);			else if($target[5] < $target[6])				imagettftext($im,12,0,10,18,$green,"./images/segoeUI.ttf",$name);			else				imagettftext($im,12,0,10,18,$white,"./images/segoeUI.ttf",$name);			imagettftext($im,10,0,30,42,$white,"images/segoeUI.ttf",$players);			imagettftext($im,10,0,100,42,$white,"images/segoeUI.ttf",$passed);			imagettftext($im,10,0,157,42,$white,"images/segoeUI.ttf",$dedi);			imagettftext($im,10,0,212,42,$white,"images/segoeUI.ttf",$bricks);			imagettftext($im,10,0,295,42,$white,"images/segoeUI.ttf",$map);		}		else		{			$im = imagecreatefrompng("./images/template3.png");			$white = imagecolorallocate($im,255,255,255);			imagesavealpha($im,true);			$name = isSet($_GET["h"]) ? $_GET["h"] . "'s Server" : $_GET["a"];			if(isSet($_GET["n"]))				$name = $_GET["n"];			$dedi = "N/A";			$passed = "N/A";			$players = "N/A";			$bricks = "N/A";			$map = "N/A";			imagettftext($im,12,0,10,18,$white,"images/segoeUI.ttf",$name);			imagettftext($im,10,0,30,42,$white,"images/segoeUI.ttf",$players);			imagettftext($im,10,0,100,42,$white,"images/segoeUI.ttf",$passed);			imagettftext($im,10,0,157,42,$white,"images/segoeUI.ttf",$dedi);			imagettftext($im,10,0,212,42,$white,"images/segoeUI.ttf",$bricks);			imagettftext($im,10,0,295,42,$white,"images/segoeUI.ttf",$map);		}		break;			case 4:		$im = imagecreatefrompng("./images/template4.png");		$white = imagecolorallocate($im, 65, 65, 65);		$black = imagecolorallocate($im, 45, 45, 45);		$fontReg = "./images/rakesly.ttf";		$fontBold = "./images/rakeslyb.ttf";		imagesavealpha($im,true);					if(count($target) > 1)		{			$name = $target[4];			if(isSet($_GET['n']))				$name = $_GET['n'];						$dedi = "Dedicated: " . ($target[3] == 1 ? "Yes" : "No");			$passed = "Passworded: " . ($target[2] == 1 ? "Yes" : "No");			$players = "Players: " . $target[5] . "/" . $target[6];			$bricks = "Bricks: " . $target[8];			$gamemode = "Gamemode: " . $target[7];		}		else		{			$name = isSet($_GET["h"]) ? $_GET["h"] . "'s Server" : $_GET["a"];			if(isSet($_GET["n"]))				$name = $_GET["n"];						$dedi = "Dedicated: N/A";			$passed = "Passworded: N/A";			$players = "Players: N/A";			$bricks = "Bricks: N/A";			$gamemode = "Gamemode: N/A";		}				imagettftext($im, 14, 0, 3, 21, $black, $fontBold, $name);		imagettftext($im, 15, 0, 10, 52, $white, $fontReg, $dedi);		imagettftext($im, 15, 0, 10, 88, $white, $fontReg, $passed);		imagettftext($im, 15, 0, 10, 125, $white, $fontReg, $players);		imagettftext($im, 15, 0, 10, 162, $white, $fontReg, $bricks);		imagettftext($im, 15, 0, 10, 199, $white, $fontReg, $gamemode);				break;			case 5:		$im = imagecreatefrompng("./images/template5.png");		$white = imagecolorallocate($im, 65, 65, 65);		$black = imagecolorallocate($im, 45, 45, 45);		$fontReg = "./images/rakesly.ttf";		$fontBold = "./images/rakeslyb.ttf";		imagesavealpha($im,true);					if(count($target) > 1)		{			$name = $target[4];			if(isSet($_GET['n']))				$name = $_GET['n'];						$dedi = ($target[3] == 1 ? "Yes" : "No");			$passed = ($target[2] == 1 ? "Yes" : "No");			$players = $target[5] . "/" . $target[6];			$bricks = $target[8];			$gamemode = $target[7];		}		else		{			$name = isSet($_GET["h"]) ? $_GET["h"] . "'s Server" : $_GET["a"];			if(isSet($_GET["n"]))				$name = $_GET["n"];						$dedi = "N/A";			$passed = "N/A";			$players = "N/A";			$bricks = "N/A";			$gamemode = "N/A";		}				imagettftext($im, 14, 0, 3, 21, $white, $fontBold, $name);		imagettftext($im, 15, 0, 26, 47, $white, $fontReg, $players);		imagettftext($im, 15, 0, 89, 47, $white, $fontReg, $passed);		imagettftext($im, 15, 0, 151, 47, $white, $fontReg, $dedi);		imagettftext($im, 15, 0, 215, 47, $white, $fontReg, $bricks);		imagettftext($im, 15, 0, 282, 47, $white, $fontReg, $gamemode);				break;}if($im){	header("Content-type: image/" . $_GET["i"]);	switch($_GET["i"])	{		case "png":			imagepng($im);			break;		case "gif":			imagegif($im);			break;		case "jpeg":			imagejpeg($im);			break;	}	imagedestroy($im);}?>