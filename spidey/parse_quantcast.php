<?php
$file_handle = fopen("Quantcast-Top-Million.txt", "r");

while (!feof($file_handle)) {
	$line = fgets($file_handle);
	if(preg_match('/^\d+/',$line)) { # if it starts with some amount of digits
		$tmp = explode("\t",$line);
		$val = trim($tmp[1]);
		if($val != 'Hidden profile') { # Hidden profile appears sometimes just ignore then
			echo 'http://'.$val."/\n";
		}
	}
}
fclose($file_handle);
?>