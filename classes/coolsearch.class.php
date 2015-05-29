<?php
include_once(dirname(__FILE__).'/../interfaces/isearch.php');
include_once(dirname(__FILE__).'/../interfaces/iranker.php');
include_once(dirname(__FILE__).'/../classes/dummyindex.class.php');

define('SEARCH_DOCUMENTRETURN', 20);

class coolsearch implements isearch {
	public $index = null;
	public $documentstore = null;
	public $ranker = null;

	function __construct(iindex $index, idocumentstore $documentstore, iranker $ranker) {
		$this->index = $index;
		$this->documentstore = $documentstore;
		$this->ranker = $ranker;
	}


	function dosearch($searchterms) {
		$indresult = array(); // AND results 
		$indorresult = array(); // OR results IE everything
		
		$interlists = array();
		
		foreach($this->_cleanSearchTerms($searchterms) as $term) {
			
			$ind = $this->index->getDocuments($term);
			if($ind != null) {
				usort($ind, array($this->ranker, 'rankDocuments'));
				$tmp = array();
				foreach($ind as $i) {
					$indorresult[$i[0]] = $i[0];
					$tmp[] = $i[0];
				}
				$interlists[] = $tmp;
			}
		}
		
		// Get the intersection of the lists
		$indresult = $interlists[0];
		foreach($interlists as $lst) {
			$indresult = array_intersect($indresult, $lst);
		}
		
		
		$doc = array();
		$count = 0;
		foreach($indresult as $i) {
			$doc[] = $this->documentstore->getDocument($i);
			$count++;
			if($count == SEARCH_DOCUMENTRETURN) {
				break;
			}
		}
		if($count != SEARCH_DOCUMENTRETURN) { // If we dont have enough results to AND default to OR
			foreach($indorresult as $i) {
				$tmp = $this->documentstore->getDocument($i);
				if(!in_array($tmp, $doc)) { # not already in there
					$doc[] = $tmp;
					$count++;
					if($count == SEARCH_DOCUMENTRETURN) {
						break;
					}
				}
			}
		}

		return $doc;
	}
	
  
	function _cleanSearchTerms($searchterms) {
		$cleansearchterms = strtolower($searchterms);
		$cleansearchterms = preg_replace('/\W/i',' ',$cleansearchterms);
		$cleansearchterms = preg_replace('/\s\s+/', ' ', $cleansearchterms);
		return explode(' ',trim($cleansearchterms));
	}
}
?>
