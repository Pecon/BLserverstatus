<?PHP
	if(!isSet($_POST['name']) && !isSet($_POST['IP']))
	{
		exit("No variables specified!");
		return;
	}

	if($_POST['type'] < 2 || $_POST['type'] > 5)
		$_POST['type'] = 3;

	if(isSet($_POST['name']))
	{
		$statusImage = "http://serverstatus.block.land/?h=" . $_POST['name'] . "&t=" . $_POST['type'];
	}
	else if(isSet($_POST['IP']) && isSet($_POST['port'] ))
	{
		$statusImage = "http://serverstatus.block.land/?a=" . $_POST['IP'] . "&p=" . $_POST['port'] . "&t=" . $_POST['type'];
	}
	else
	{
		$statusImage = "http://serverstatus.block.land/?a=" . $_POST['IP'] . "&t=" . $_POST['type'];
	}

	echo('
			<html>
				<head>
					<title>Blockland status image generator</title>
					<meta name="author" content="Pecon7">
				</head>
				<style>
					body{background-color:#BBB;}
					textarea{width:700px;}
				</style>
				<body>
					<center>
					Use the following url as your server status image.<br>
					<textarea readonly rows=1 autocomplte="off">' . $statusImage . '</textarea><br>
					If you\'re posting this image on the forums, don\'t forget to use the [img] tags to embed the image in your post.<br>

					<br>
					Here is a preview of your server status image. If it does not show your server then you may have entered some info wrong (Or your server actually isn\'t up right now).<br>
					<img src="' . $statusImage . '" alt="">
					</center>
				</body>
			</html>
		');
?>
