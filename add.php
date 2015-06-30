<?php
// <time-out>
set_time_limit(0);
// <debug>
error_reporting(0);
// Memory limit
ini_set('memory_limit','7G');
// constants: PATHs to the index and doc directories
define('INDEXLOCATION',dirname(__FILE__).'/index/');
define('DOCUMENTLOCATION',dirname(__FILE__).'/documents/');

// dependencies
include_once('./classes/coolindexer.class.php');
include_once('./classes/dummysearch.class.php');
include_once('./classes/coolindex.class.php');
include_once('./classes/cooldocumentstore.class.php');
include_once('./classes/dummyranker.class.php');

// vars
$index = new coolindex();
$docstore = new cooldocumentstore();
$ranker = new dummyranker();
$indexer = new coolindexer($index,$docstore,$ranker);
$search = new dummysearch($index,$docstore,$ranker);

// parse a HTML source file
function html2txt($document){ 
	$searchitem = array('@<script[^>]*?>.*?</script>@si',  			// Javascript 
					'@<[\/\!]*?[^<>]*?>@si',            	// HTML tags 
					'@<style[^>]*?>.*?</style>@siU',    	// Other tags 
					'@<![\s\S]*?--[ \t\n\r]*>@',        	// CDATA 
					'@<style[^>]*?>.*?</style>@si',        	// CSS 
					'@\W+@si',        			// Strip Whitespace
	);
	// Regex everything with a whitespace
	$text = preg_replace($searchitem, ' ', $document); 
	return $text; 
} 

// index buffer
$toindex = array();

// counter of max iterations
$count = 0;

// create the documents directory for storing
if(!file_exists('./documents/')){
	mkdir('./documents/');
}
// Create the index directory for storing
if(!file_exists('./index/')){
	mkdir('./index/');
}

// For every document in the document directory (source retrieved by the spidey crawler)
foreach(new RecursiveIteratorIterator (new RecursiveDirectoryIterator ('./spidey/documents/')) as $x) {
	// Define the path to the file
	$filename = $x->getPathname();
	// if the file exists
	if(is_file($filename)) {
		// Read-only file
		$handle = fopen($filename, 'r');
		// Get the contents (URL + content) of the file
		$contents = fread($handle, filesize($filename));
		// Close the file
		fclose($handle);
		// PHP readable file
		$unserialized = unserialize($contents);
		// Strip the document
		$url = $unserialized[0];		// URL
		$content = $unserialized[1];		// Content
		// Banner for information
                echo 'Parsing>> '.$url."\r\n";
		// General HTML parsing
		preg_match_all('/<title.*?>.*?<\/title>/i',$content, $matches);
		$title = trim(strip_tags($matches[0][0]));
		$tmp = get_meta_tags("data://$mime;base64,".base64_encode($content));
		// Get the description of the HTML document
		$desc = trim($tmp['description']);
		// More parsing (with html2txt)
		$content = trim(strip_tags(html2txt($content)));
		// Reduce the description to 200 chars
		if($desc == '' && $content != '') {
			$desc = substr($content,0,200).'...';
		}
		// Reduce the title to 50 chars
		if($title == '' && $desc != '') {
			$title = substr($desc,0,50).'...';
		}
		// Resets the content for the next iteration
		$content = '';
		// Add the general info to the index var
		echo "****** $title\r\n";
		if($title != '') {
			$toindex[] = array($url, $title, $desc, $content);
		}
		// Max entries counter increase
		$count++;
		// If counter reaches max: bye bye
		if($count == 20000)
			break;
		
	}
}

// Index everything away!
$indexer->index($toindex);

?>
