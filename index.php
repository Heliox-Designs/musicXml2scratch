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
	$xml = simplexml_load_string(file_get_contents("feliz.xml"));
	
	//Get basic data
	$title		= "UNKNOWN";
	$src		= $xml -> identification -> source;
	$software	= $xml -> encoding -> software;
	$tones		= array();
	
	//Read the notes
	$instruments = 0;
	foreach($xml -> part as $instrument)	{
		//Loop through this instrument
		$instruments++;
		foreach($instrument -> measure as $measure)	{
			//Loop through notes
			foreach($measure -> note as $note)	{
				if(isset($note -> rest))	{
					//This is a rest
					$length	= (string) $note -> duration;
					//Insert
					$voice = (string) $note -> voice;
					$tones[$instruments][] = array(0, $length);
				}
				elseif(isset($note -> pitch))	{
					//This is a note
					$length	= (string) $note -> duration;
					$step	= (string) $note -> pitch -> step;
					$alter	= (string) $note -> pitch -> alter or "0";
					$value	= intval($note -> pitch -> octave) * 12 + intval($t[$step]) + intval($alter);
					//Insert
					$voice = (string) $note -> voice;
					$tones[$instruments][] = array($value, $length, $step, $note -> pitch -> octave);
				}
			}
		}
	}
	
	//Create output
	$files = array();
	for($i = 1; $i <= $instruments; $i++)	{
		$files[$i]["values"] = fopen("values". $i . ".txt", "w");
		$files[$i]["lengths"] = fopen("lengths". $i . ".txt", "w");
		
		foreach($tones[$i] as $value)	{
			fwrite($files[$i]["values"], $value[0] . "\n");
		}
		foreach($tones[$i] as $length)	{
			fwrite($files[$i]["lengths"], $length[1] . "\n");
		}
		
		fclose($files[$i]["values"]);
		fclose($files[$i]["lengths"]);
	}
	
	//Print file
	echo "<pre>";
	print_r($xml);
?>