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
	if(!isset($xml -> part)) {
		die("Invalid file");
	}

	//Read the notes
	$instruments = 0;
	foreach($xml -> part as $instrument) {
		//Loop through this instrument
		$instruments++;
		foreach($instrument -> measure as $measure)	{
			//Clear note's last x position and last staff
			$last_x_pos = 0;
			$last_staff = 1;
			
			//Loop through notes
			foreach($measure -> note as $note) {
				//We need it
				$length	= (string) $note -> duration;
				$staff = isset($note -> staff) ? intval($note -> staff) : 1;
				if ($last_staff !== $staff) { $last_x_pos = 0; $last_staff = $staff; } // If in one measure are 2 staffs
				
				//Check if note exists
				if (isset($note -> pitch)) {
					//This is a note
					$step	= (string) $note -> pitch -> step;
					$alter	= (string) $note -> pitch -> alter or "0";
					$value	= intval($note -> pitch -> octave) * 12 + intval($t[$step]) + intval($alter);
					
					//Does note have attributes?
					if ($note -> attributes() -> count() > 1) {
						//Exporting attributes to variable
						$attr = $note -> attributes();
					}
				}
				
				//Inserting notes
				if(isset($note -> rest)) {
					//This is a rest
					//Insert
					$tones[$instruments][$staff][] = array(0, $length);
					
					//Rest doesn't have attributes
					$last_x_pos = 0;
				}
				elseif(isset($attr) && floatval($attr[0]) == floatval($last_x_pos))	{
					//Multi-notes
					//Get last element of array
					$lp = isset($tones[$instruments][$staff]) ? count($tones[$instruments][$staff]) - 1 : 0;
					
					//Insert note
					$tmp_array = array($value, $length, $step, $note -> pitch -> octave);
					$length = count($tones[$instruments][$staff][$lp]); // Error fixed!
					for ($i = 0; $i < $length; $i++) {
						$tones[$instruments][$staff][$lp][$i] = $tones[$instruments][$staff][$lp][$i].";".$tmp_array[$i];
					}
				}
				elseif(isset($note -> pitch)) {
					//Insert note
					$tones[$instruments][$staff][] = array($value, $length, $step, $note -> pitch -> octave);
				}
				
				//Set note's last x position
				if (isset($attr)) {
					$last_x_pos = $attr[0];
				} else {
					$last_x_pos = 0;
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
				fwrite($files[$i]["lengths"], $value[1] . "\n");
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
	
	//Check if is any error
	if (count(error_get_last()) > 0) { die("<br>Please report this errors to <a href=\"https://scratch.mit.edu/users/SzAmmi\">@SzAmmi</a> or <a href=\"https://scratch.mit.edu/users/webdesigner97\">@webdesigner97</a>"); }
	
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
