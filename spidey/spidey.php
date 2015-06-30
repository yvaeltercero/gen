<?php
/*
** Spidey web crawler
** It (w)gets a source from a URL address
** Written to work with threads (xargs)
** Run: "cat urls.txt | xargs -L1 -P32 php spidey.php"
*/
	// <debug>
	error_reporting(E_ERROR | E_PARSE);

	// URL string preparation
	$url = trim($argv[1]);
	$md5 = md5($url);
	// 2 fase hash
	$one = substr($md5,0,2);
	$two = substr($md5,2,2);
	// Create repository for downloaded documents
	if(!file_exists('./documents/')){
		mkdir('./documents/');
	}

	// If the dir doesn't exist, create it
	if(!file_exists('./documents/'.$one.'/'.$two.'/'.$md5)) {
		echo 'Downloading - '.$url."\r\n";
		// GET the source of the URL
		$content = file_get_contents($url);
		// Create an array with the URL and the content
		// Representation of the doc
		$document = array($url,$content);
		// Makes a storable version of the doc
		// To save in disk
		$serialized = serialize($document);
		// If the hash-path to the doc doesnt exist
		// Create it
		if(!file_exists('./documents/'.$one.'/')){
			mkdir('./documents/'.$one.'/');
		}
		if(!file_exists('./documents/'.$one.'/'.$two.'/')){
			mkdir('./documents/'.$one.'/'.$two.'/');
		}
		// Open the writable file
		$fp = fopen('./documents/'.$one.'/'.$two.'/'.$md5, 'w');
		// Write the content in the file
		fwrite($fp, $serialized);
		// Close the file
		fclose($fp);
	}
?>
