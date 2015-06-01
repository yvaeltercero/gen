<?php
set_time_limit(0);
error_reporting(0);
define('INDEXLOCATION',dirname(__FILE__).'/index/');
define('DOCUMENTLOCATION',dirname(__FILE__).'/documents/');

include_once('./classes/coolindexer.class.php');
include_once('./classes/dummysearch.class.php');
include_once('./classes/coolindex.class.php');
include_once('./classes/cooldocumentstore.class.php');
include_once('./classes/dummyranker.class.php');

$index = new coolindex();
$docstore = new cooldocumentstore();
$ranker = new dummyranker();
$indexer = new coolindexer($index,$docstore,$ranker);
$search = new dummysearch($index,$docstore,$ranker);


function html2txt($document){ 
	$searchitem = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript 
					'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags 
					'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly 
					'@<![\s\S]*?--[ \t\n\r]*>@',        // Strip multi-line comments including CDATA 
					'@<style[^>]*?>.*?</style>@si',        // Strip CSS 
					'@\W+@si',        // Strip Whitespace
	); 
	$text = preg_replace($searchitem, ' ', $document); 
	return $text; 
} 


$toindex = array();

$count = 0;

foreach(new RecursiveIteratorIterator (new RecursiveDirectoryIterator ('./spidey/documents/')) as $x) {
	$filename = $x->getPathname();
	if(is_file($filename)) {
		$handle = fopen($filename, 'r');
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		$unserialized = unserialize($contents);
		
		$url = $unserialized[0];
		$content = $unserialized[1];
		
		preg_match_all('/<title.*?>.*?<\/title>/i',$content, $matches);
		$title = trim(strip_tags($matches[0][0]));
		
		// Turns out PHP has a function for extracting meta tags for us, the only
		// catch is that it works on files, so we fake a file by creating one using
		// base64 encode and string concaternation
		$tmp = get_meta_tags("data://$mime;base64,".base64_encode($content));
		$desc = trim($tmp['description']);
		
		// This is the rest of the content. We try to clean it somewhat using
		// the custom function html2text which works 90% of the tiem
		$content = trim(strip_tags(html2txt($content)));
		
		// If values arent set lets try to set them here. Start with desc
		// using content and then try the title using desc
		if($desc == '' && $content != '') {
			$desc = substr($content,0,200).'...';
		}
		if($title == '' && $desc != '') {
			$title = substr($desc,0,50).'...';
		}
		
		$content = '';
		
		// If we dont have a title, then we dont have desc or content
		// so lets not add it to the index
		if($title != '') {
			$toindex[] = array($url, $title, $desc, $content);
		}
		
		$count++;
		
		if($count == 20000)
			break;
		
	}
}

$indexer->index($toindex);

?>
