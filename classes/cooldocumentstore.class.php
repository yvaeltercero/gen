<?php
/*
** Document store: manages document files retrieved by the spidey
*/

// dependencies
include_once(dirname(__FILE__).'/../interfaces/idocumentstore.php');

// constants: file extention (plain text)
define('DOCUMENTSTORE_DOCUMENTFILEEXTENTION', '.txt');

class cooldocumentstore implements idocumentstore {
	// Constructor: verifies the constraints
	// for the creation of the object
	function __construct() {
		$this->_checkDefinitions();
	}

	// Save a document in disk
	public function storeDocument(array $document=null) {
		// If the document is not empty or null
		if(!is_array($document) || count($document) == 0) {
			return false;
		}
		// Document unique representation
		$docid = $this->_getNextDocumentId();
		// Convertion to storable format
		$serialized = serialize($document);
		// Open the file according to its path
		$fp = fopen($this->_getFilePathName($docid), 'a');
		// Write the contents in it
		fwrite($fp, $serialized);
		// Close it
		fclose($fp);
		// Returns the ID
		return $docid;
	}
  
	// Get PHP readable version of a document
	public function getDocument($documentid) {
		// If the ID argument is invalid, arrivederci
		if(!is_integer($documentid) || $documentid < 0) {
			return null;
		}
		// Get the filename according to the ID
		$filename = $this->_getFilePathName($documentid);
		// If the file does not exist, chao
		if (!file_exists($filename)) {
		  return null;
		}
		// Open a readable version of the file
		$handle = fopen($filename, 'r');
		// Gets the contents
		$contents = fread($handle, filesize($filename));
		// Close it
		fclose($handle);
		// Converts it to a PHP readable version
		$unserialized = unserialize($contents);
		// Return it
		return $unserialized;
	}
  
	// Delete all files of the document repository
	public function clearDocuments() {
		// Open the repo directory
		$fp = opendir(DOCUMENTLOCATION);
		// While there is files in the dir
		while(false !== ($file = readdir($fp))) {
			// If the file exists
			if(is_file(DOCUMENTLOCATION.$file)){
				// Delete it nicely
				unlink(DOCUMENTLOCATION.$file);
			}
		}
	}
  
	// Check the constraints necessary for the object documentstore to work
	public function _checkDefinitions() {
		// If the global var DOCUMENT LOCATION is not defined
		if(!defined('DOCUMENTLOCATION')) {
			// !!! ERROR !!!
			throw new Exception('Expects DOCUMENTLOCATION to be defined!');
		}
	}
  
	// Get the file path according to his hash function
	public function _getFilePathName($name) {
		// Get the MD5 conversion
		$md5 = md5($name);
		// Get the first 2 chars
		$one = substr($md5,0,2);
		// Looks for the location of the doc directory
		if(!file_exists(DOCUMENTLOCATION.$one.'/')){
			mkdir(DOCUMENTLOCATION.$one.'/');
		}
		// Return the relative path to the file in the filesystem
		return DOCUMENTLOCATION.$one.'/'.$name.DOCUMENTSTORE_DOCUMENTFILEEXTENTION;
	}
  
	// Get the ID of the next file to be processed
	public function _getNextDocumentId() {
		// Counter according to the value of the doc counter
		$countFile = $this->_getFilePathName('__count__');
		// Counter of the next document ID
		$count = 0;
		// If the file exists
		if(file_exists($countFile)) {
			// Open a read-only buffer
			$fh = fopen($countFile, 'r');
			// Get the ID
			$count = (int)fgets($fh);
		}
		// Open a writable version of the buffer
		$fh = fopen($countFile, 'w');
		// Sets the ID in the file
		fputs($fh,$count+1);
		// Close the file
		fclose($fh);
		// Return the ID
		return $count;
	}
}
?>
