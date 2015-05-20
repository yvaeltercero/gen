<?php

include_once dirname(__FILE__).'/../interfaces/isearch.php';
include_once dirname(__FILE__).'/../interfaces/iranker.php';
include_once dirname(__FILE__).'/../classes/index.class.php';

define('SEARCH_DOCUMENTRETURN', 20);

class search implements isearch{

	public $index = null;
	public $documentstore = null;
	public $ranker = null;

	function __construct(iindex $index, idocumentstore $documentstore, iranker $ranker){
		$this->index = $index;
		$this->documentstore = $documentstore;
		$this->ranker = $ranker;
	}

	function dosearch($searchterms){
		$indresult = array();
		$indorresult = array();
		$interlists = array();

		foreach($this->_cleanSearchTerms($searchterms) as $term){
			$ind = $this->index->getDocuments($terms);
			if($ind != null){
				usort($ind, array($this->ranker, 'rankDocuments'));
				$tmp = array();
				foreach($ind as $i){
					$indorresult[$i[0]] = $i[0];
					$tmp[] = $i[0];
				}
				$interlists[] = $tmp;
			}
		}

		$indresult = $interlists[0];
		foreach($interlists as $lst){
			$indresult = array_intersect($indresult, $lst);
		}

		$doc = array();
		$count = 0;
		foreach($indresult as $i){
			$doc[] = $this->documentstore->getDocument($i);
			$count++;
			if($count == SEARCH_DOCUMENTRETURN){
				break;
			}
		}
		if($count != SEARCH_DOCUMENTRETURN){
			foreach($indorresult as $i){
				$tmp = $this->documentstore->getDocument($i);
				if(!in_array($tmp,$doc)){
					$doc[] = $tmp;
					$count++;
					if($count == SEARCHDOCUMENTRETURN){
						break;
					}
				}
			}
		}
		return $doc;
	}

	function _cleanSearchTerms($searchterms){
		$cleansearchterms = strtolower($searchterms);
		$cleansearchterms = preg_replace('/\W/i', ' ', $cleansearchterms);
		$cleansearchterms = preg_replace('/\s\s+/', ' ', $cleansearchterms);
		return explode(' ', trim($cleansearchterms));
	}

}

?>
