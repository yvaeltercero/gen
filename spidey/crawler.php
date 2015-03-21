<?php
	$url = trim($argv[1]);

	$md5 = md5($url);
	$one = substr($md5,0,2);
	$two = substr($md5,2,2);

	if(!file_exists('./documents/'.$one.'/'.$two.'/'.$md5)) {
		echo 'Downloading - '.$url."\r\n";
		$content = file_get_contents($url);
		$document = array($url,$content);
		$serialized = serialize($document);
		mkdir('./documents/');
		mkdir('./documents/'.$one.'/');
		mkdir('./documents/'.$one.'/'.$two.'/');
		$fp = fopen('./documents/'.$one.'/'.$two.'/'.$md5, 'w');
		fwrite($fp, $serialized);
		fclose($fp);
	}
?>
