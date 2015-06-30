<?php
/*
** Rank an index entry according the numbers of appeareances in a document
*/

// dependencies
include_once(dirname(__FILE__).'/../interfaces/iranker.php');

class dummyranker implements iranker {

	// Constructor: nothing to do
	function __construct() {
		// nada
	}
  
	// Rank a document according to its popularity
	public function rankDocuments($document,$document2) {
		// If any of the documents is an array
		if(!is_array($document) || !is_array($document2)) {
			// !!! ERROR !!!
			throw new Exception('Document(s) not array!');
		}
		// If the size of the document array is incorrect
		if(count($document) != 3 || count($document2) != 3) {
			// !!! ERROR !!!
			throw new Exception('Document not correct format!');
		}
		// If the first document popularity is equal with the second
		if($document[1] == $document2[1] ) {
			return 0;
		}
		// If the first document's popularity is lower than the second
		if($document[1] <= $document2[1] ) {
			return 1;
		}
		// If the first document's popularity is higher than the second
		return -1;
	}
}
?>
