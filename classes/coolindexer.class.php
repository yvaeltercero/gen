<?php
/*
** Class that fills the index structure according to the document repository
*/

// dependencies
include_once(dirname(__FILE__).'/../interfaces/iindexer.php');
include_once(dirname(__FILE__).'/../interfaces/iranker.php');
include_once(dirname(__FILE__).'/../classes/dummyindex.class.php');

error_reporting(00);

class coolindexer implements iindexer {
	// vars
	public $index = null;
	public $documentstore = null;
	public $ranker = null;

	// Constructor: requires an index, a documentstore and a ranker as parameter
	// Initialize the object and its dependencies
	function __construct(iindex $index,idocumentstore $documentstore, iranker $ranker) {
		$this->index = $index;
		$this->documentstore = $documentstore;
		$this->ranker = $ranker;
	}
  
	// Index an array of documents
	public function index(array $documents) {
		// If the documents is not an array
		if(!is_array($documents)) {
			return false;
		}
		
		// Create an array of hash for storing
		$documenthash = array();

		// For every document in the array
		foreach($documents as $document) {
			echo "Indexing>> $document[0]\r\n";
			// Get the document ID
			$id = $this->documentstore->storeDocument(array($document));
			// Clean the document for processing
			$con = $this->_concordance($this->_cleanDocument($document));
			// For every word in the cleaned file
			foreach($con as $word => $count) {
				// If the word is not duplicated
				if(!array_key_exists($word,$documenthash)) {
					// Save the word 
					$ind = $this->index->getDocuments($word);
					// Register the word hash in the array
					$documenthash[$word] = $ind;
				}
				// If the array is empty	
				if(count($documenthash[$word]) == 0) {
					// Save the word definition instead
					$documenthash[$word] = array(array($id,$count,0));
				}
				else {
					// If not, save only the counter
					$documenthash[$word][] = array($id,$count,0);
				}
			}
		}
		
		// For every entry in the hash
		foreach($documenthash as $key => $value) {
			// Sort the ranked value in the value var
			usort($value, array($this->ranker, 'rankDocuments'));
			// Store the file in the index
			$this->index->storeDocuments($key,$value);
		}
		// leave with success
		return true;
	}

	// Return an alphabetical array of the words present in a document
	public function _concordance(array $document) {
		// If the document can't be read as an array
		if(!is_array($document)) {
			// Return an empty array
			return array();
		}
		// Initialize redefinition
		$con = array();
		// For every token of the document
		foreach($document as $word) {
			// Check if the a word exists in a document array
			if(array_key_exists($word,$con)) {
				// Increase the counter
				$con[$word] = $con[$word] + 1;
			}
			else {
				// Set it as the lowest value
				$con[$word] = 1;
			}
		}
		// Return the concordance
		return $con;
	}
  
	// Clean unwanted grease of a document
	public function _cleanDocument($document) {
		// Unify the contents of the document
		$contents = $document[0].' '.$document[1].' '.$document[2];
		// Strip HTML and PHP tags
		$cleandocument = strip_tags(strtolower($contents));
		// Replace non-alphanumerical characters
		$cleandocument = preg_replace('/\W/i',' ',$cleandocument);
		// Replace excess whitespace
		$cleandocument = preg_replace('/\s\s+/', ' ', $cleandocument);
		// If the document is not empty
		if($cleandocument != ''){
			// split a string by tokens separated for whitespaces
			return explode(' ',trim($cleandocument));
		}
		return array();
	}
}
?>
