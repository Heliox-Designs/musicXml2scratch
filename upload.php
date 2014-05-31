<?php
	$upload = $_FILES["mxlxml"];
	$start = microtime();
	
	//Check Mime Type
	if($upload["type"] == "text/xml")	{
		//It's XML
		$path = "tmp/" . $start;
		$contents = file_get_contents($upload["tmp_name"]);
		mkdir($path);
	}
	elseif(is_resource(zip_open($upload["tmp_name"])))	{
		//It's zipped
		//Unzip
		$path = "tmp/" . $start;
		$zip = new ZipArchive;
		$zip -> open($upload["tmp_name"]);
		$zip -> extractTo($path);
		//delete unused stuff
		unlink($path . "/META-INF/container.xml");
		rmdir($path . "/META-INF");
		//Get content
		$dir = scandir($path);
		$contents = file_get_contents($path . "/" . $dir[2]);
	}
	else	{
		//It's something else
		die("Invalid file");
	}
	//Files checked, content read. Now convert!
	require_once("lib/convert.php");
?>