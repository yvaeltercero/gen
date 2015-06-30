<?php
/*
** Search: do an iterative search over the index and document repositories
*/

// dependencies
include_once(dirname(__FILE__).'/../interfaces/isearch.php');
include_once(dirname(__FILE__).'/../interfaces/iranker.php');
include_once(dirname(__FILE__).'/../classes/coolindex.class.php');

// constants: maximum of entries returned
define('SEARCH_DOCUMENTRETURN', 20);

class coolsearch implements isearch {
	// vars
	public $index = null;
	public $documentstore = null;
	public $ranker = null;

	// Constructor: require an index, a documentstore and a ranker arguments for the object creation
	function __construct(iindex $index, idocumentstore $documentstore, iranker $ranker) {
		$this->index = $index;
		$this->documentstore = $documentstore;
		$this->ranker = $ranker;
	}

	// Do a search over the index and document repositories
	// It also ranks the documents
	function dosearch($searchterms) {
		// AND results
		$indresult = array();
		// OR results
		$indorresult = array();
		// Intersection relation results
		$interlists = array();
		// For every token of the search query
		foreach($this->_cleanSearchTerms($searchterms) as $term) {
			// Looks for the index entries with term searched
			$ind = $this->index->getDocuments($term);
			// If it returns results
			if($ind != null) {
				// Sort the the term by appearance
				usort($ind, array($this->ranker, 'rankDocuments'));
				// Create a buffer array
				$tmp = array();
				// For every index entry
				foreach($ind as $i) {
					// Save it in the OR results
					$indorresult[$i[0]] = $i[0];
					// Save it in the temporary buffer
					$tmp[] = $i[0];
				}
				// Save the temporary in the intersection
				$interlists[] = $tmp;
			}
		}
		
		// Save the intersection in the AND results
		$indresult = $interlists[0];
		// For every result in the intersection
		foreach($interlists as $lst) {
			// Compares and saves in the AND results
			$indresult = array_intersect($indresult, $lst);
		}
		
		// Result return array
		$doc = array();
		// counter of maximum returned documents
		$count = 0;

		// For every result in the AND results
		foreach($indresult as $i) {
			// Get the document and save it in the result return array
			$doc[] = $this->documentstore->getDocument($i);
			// Increase the max counter
			$count++;
			// If the counter reach the max, take a walk
			if($count == SEARCH_DOCUMENTRETURN) {
				break;
			}
		}
		// If it have not reached the max yet
		if($count != SEARCH_DOCUMENTRETURN) {
			// For every result in the OR results
			foreach($indorresult as $i) {
				// Save in a temporary array the documents retrieved
				$tmp = $this->documentstore->getDocument($i);
				// If the results are not already listed in the results
				if(!in_array($tmp, $doc)) {
					// Save the result in the result return array
					$doc[] = $tmp;
					// Increase the max counter
					$count++;
					// If the counter reach the max, au revoir
					if($count == SEARCH_DOCUMENTRETURN) {
						break;
					}
				}
			}
		}
		// Return the result return array
		return $doc;
	}
	
  	// Clean the search terms of the query
	function _cleanSearchTerms($searchterms) {
		// Pass it all to lowercase
		$cleansearchterms = strtolower($searchterms);
		// Replace every non-alphanumerical characters
		$cleansearchterms = preg_replace('/\W/i',' ',$cleansearchterms);
		// Replace excess whitespace
		$cleansearchterms = preg_replace('/\s\s+/', ' ', $cleansearchterms);
		// Split the strings into tokens and return the array
		return explode(' ',trim($cleansearchterms));
	}
}
?>
