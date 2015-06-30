<?php
/*
** Manages index files processed from the doc repository
*/

// dependencies: class definiton
include_once(dirname(__FILE__).'/../interfaces/iindex.php');

// constants
define('DOCUMENTCOUNT', 3);
define('DOCUMENTBYTESIZE', 12);
define('DOCUMENTINTEGERBYTESIZE', 4);
define('DOCUMENTFILEEXTENTION', '.bin');
define('DOCUMENTRETURN', 200);

class coolindex implements iindex {
	// Constructor: checks contraints
	// required for the object to work
	function __construct() {
		$this->_checkDefinitions();
	}

	// Store an array of index documents in a binary format
	public function storeDocuments($name, array $documents = null) {
		// If the documents is not defined or empty, sayonara
		if($name === null || $documents === null || trim($name) == '') {
			return false;
		}
		// If the file info is invalid, adiós
		if(!is_string($name) || !is_array($documents)) {
			return false;
		}
		// For each document to save as index
		foreach($documents as $doc) {
			// Validate every single one
			if(!$this->validateDocument($doc)){
				return false;
			}
		}
		// Open the file definition to write
		$fp = fopen($this->_getFilePathName($name),'w');
		// For every document to convert to index format
		foreach($documents as $doc) {
			// Pack the 3 parts of the doc into 3 binary strings
			$bindata1 = pack('i',intval($doc[0]));
			$bindata2 = pack('i',intval($doc[1]));
			$bindata3 = pack('i',intval($doc[2]));
			// Secuencially write the binary file
			fwrite($fp,$bindata1);
			fwrite($fp,$bindata2);
			fwrite($fp,$bindata3);
		}
		// Close the binary file
		fclose($fp);
		return true;
	}
 
	// Get a file by a name (identificator) 
	public function getDocuments($name) {
		// If the file definitions does not exist
		if(!file_exists($this->_getFilePathName($name))) {
			// Return an empty array
			return array();
		}
		// Open the file in read-only
		$fp = fopen($this->_getFilePathName($name),'r');
		// Get the filesize
		$filesize = filesize($this->_getFilePathName($name));
		
		// If the filesize is not correctly defined
		if($filesize%DOCUMENTBYTESIZE != 0) {
			// !!! ERROR !!!
			throw new Exception('Filesize not correct index is corrupt!');
		}
		// return array initialize
		$ret = array();
		// counter of maximum matches
		$count = 0;
		// For every one of the binary files
		for($i=0;$i<$filesize/DOCUMENTBYTESIZE;$i++) {
			// Read the 3 parts of the bin file
			$bindata1 = fread($fp,DOCUMENTINTEGERBYTESIZE);
			$bindata2 = fread($fp,DOCUMENTINTEGERBYTESIZE);
			$bindata3 = fread($fp,DOCUMENTINTEGERBYTESIZE);
			// Unpack data from the binary the binary string
			$data1 = unpack('i',$bindata1);
			$data2 = unpack('i',$bindata2);
			$data3 = unpack('i',$bindata3);
			// Fill the return array 
			$ret[] = array($data1[1],
							$data2[1],
							$data3[1]);
			// Increase the max counter
			$count++;
			// If the counter reaches the max, hasta la vista
			if($count == DOCUMENTRETURN) {
				break;
			}
		}
		// Close the buffer file
		fclose($fp);
		// Return the array with the indexes
		return $ret;
	}
  
	// Remove all the files in the index repository
	public function clearIndex() {
		// Open the index directory
		$fp = opendir(INDEXLOCATION);
		// While there is files in the directory
		while(false !== ($file = readdir($fp))) {
			// If the file to be removed exists
			if(is_file(INDEXLOCATION.$file)){
				// Delete it nicely
				unlink(INDEXLOCATION.$file);
			}
		}
	}
  
	// Verifies a document definitions with the storing conditions
	public function validateDocument(array $document=null) {
		// If the document is not an array, go home
		if(!is_array($document)) {
			return false;
		}
		// If the directory is not fragmented in 3 parts, take off
		if(count($document) != DOCUMENTCOUNT) {
			return false;
		}
		// For every part of the document
		for($i=0;$i<DOCUMENTCOUNT;$i++) {
			// If every part is not an integer or negative, time to say goodbye
			if(!is_int($document[$i]) || $document[$i] < 0) {
				return false;
			}
		}
		return true;
	}
   
	// Get the relative file path to an index file
	public function _getFilePathName($name) {
		// Get the MD5 conversion
		$md5 = md5($name);
		// Extract the first 2 chars
		$one = substr($md5,0,2);
		// If the file location does not exists
		if(!file_exists(INDEXLOCATION.$one.'/')){
			// Create the location
			mkdir(INDEXLOCATION.$one.'/');
		}
		// Return the relative path to the index file in the filesystem
		return INDEXLOCATION.$one.'/'.$name.DOCUMENTFILEEXTENTION;
	}
  
	// Verifies the constraints required of the index object to work
	public function _checkDefinitions() {
		// If the location has not been defined yet
		if(!defined('INDEXLOCATION')) {
			throw new Exception('Expects INDEXLOCATION to be defined!');
		}
	}
}
?>
