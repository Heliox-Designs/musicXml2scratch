#musicXml2scratch

musicXml2scratch converts musicXML files (see http://www.musicxml.org/)
into text files which can be imported into a Scratch (see http://scratch.mit.edu) project
from where they can just be played to your audience or even build a Synthesia, Guitar Hero,
whatever-like game.

This project is **unstable** and might/will produce strange output. For a list of known, bugs,
visit the [Issues].

##Convert a musicXML file
	1. Delete all txt files in the project's directory if present
	2. Put your name.xml into the same directory as the index.php
	3. Open index.php, go to line 14 ($xml = simplexml_load_string...)
	and replace "*.xml" with "name.xml", where name is the filename of
	your musicXML file
	4. Call the index.php with a webserver
	5. A lot of output and hopefully no error messages will appear
	6. Import the generated text files into Scratch
