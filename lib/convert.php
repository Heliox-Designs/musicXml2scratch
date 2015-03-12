<?php
	//Initialize basic stuff
	$t = array(
		"C" => 0,
		"D" => 2,
		"E" => 4,
		"F" => 5,
		"G" => 7,
		"A" => 9,
		"B" => 11,
	);

	//Load musicXML file
	$xml = simplexml_load_string($contents);
	if(!isset($xml -> part))	{
		die("Invalid file");
	}

	//Read the notes
	$instruments = 0;
	foreach($xml -> part as $instrument)	{
		//Loop through this instrument
		$instruments++;
		foreach($instrument -> measure as $measure)	{
			//Loop through notes
			foreach($measure -> note as $note)	{
				//We need it
				$length	= (string) $note -> duration;
				$voice = (string) $note -> voice;
				$staff = isset($note -> staff) ? intval($note -> staff) : 1;
			
				if(isset($note -> rest))	{
					//Insert
					$tones[$instruments][$staff][] = array(0, $length);
				}
				elseif(isset($note -> pitch))	{
					//This is a note
					$step	= (string) $note -> pitch -> step;
					$alter	= (string) $note -> pitch -> alter or "0";
					$value	= intval($note -> pitch -> octave) * 12 + intval($t[$step]) + intval($alter);
					//Insert
					$tones[$instruments][$staff][] = array($value, $length, $step, $note -> pitch -> octave);
				}
			}
		}
	}

	
	//Create tmpput
	$files = array();
	mkdir($path . "/txt");
	for($i = 1; $i <= $instruments; $i++)	{
		for($x = 1; $x <= count($tones[$i]); $x++) {
			
			$files[$i]["values"] = fopen($path . "/txt/values". $i . "hand". $x .".txt", "w");
			$files[$i]["lengths"] = fopen($path . "/txt/lengths". $i . "hand". $x .".txt", "w");
			
			foreach($tones[$i][$x] as $value)	{
				fwrite($files[$i]["values"], $value[0] . "\n");
			}
			foreach($tones[$i][$x] as $length)	{
				fwrite($files[$i]["lengths"], $length[1] . "\n");
			}
			
			fclose($files[$i]["values"]);
			fclose($files[$i]["lengths"]);
		}
	}
	
	//Zip file
	$out = new ZipArchive;
	$out -> open($path . "/lists.zip", ZIPARCHIVE::CREATE);
	
	for($i = 1; $i <= $instruments; $i++)	{
		for($x = 1; $x <= count($tones[$i]); $x++) {
			
			$out -> addFile($path . "/txt/values". $i . "hand". $x .".txt", "values". $i . "hand". $x .".txt");
			$out -> addFile($path . "/txt/lengths". $i . "hand". $x .".txt", "lengths". $i . "hand". $x .".txt");
		}
	}
	
	$out -> close();
	
	//Download
	header("Content-disposition: attachment; filename=lists.zip");
	header('Content-type: application/zip');
	echo file_get_contents($path . "/lists.zip");
	
	//Delete directory
	function destroy_dir($dir) { 
    if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
        foreach (scandir($dir) as $file) { 
            if ($file == '.' || $file == '..') continue; 
            if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) { 
                chmod($dir . DIRECTORY_SEPARATOR . $file, 0777); 
                if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) return false; 
            }; 
        } 
        return rmdir($dir); 
    }
	destroy_dir($path);
?>