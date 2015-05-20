<?php

include_once dirname(__FILE__) . '/../interfaces/iindexer.php';
include_once dirname(__FILE__) . '/../interfaces/iranker.php';
include_once dirname(__FILE__) . '/../classes/index.class.php';

class coolindexer implements iindexer {
	public $index = null;
	public $documentstore = null;
	public $ranker = null;

	function __construct(iindex $index, idocumentstore $documentstore, iranker $ranker) {
		$this->index = $index;
		$this->documentstore = $documentstore;
		$this->ranker = $ranker;
	}

	public function index(array $documents) {
		if (!is_array($documents)) {
			return false;
		}

		$documenthash = array();

		foreach ($documents as $document) {
			// Save the document and its ID then clean it up for processing
			$id = $this->documentstore->storeDocument(array($document));
			$con = $this->_concordance($this->_cleanDocument($document));

			foreach ($con as $word => $count) {
				// Get and cache the word if we dont have it
				if (!array_key_exists($word, $documenthash)) {
					$ind = $this->index->getDocuments($word);
					$documenthash[$word] = $ind;
				}

				if (count($documenthash[$word]) == 0) {
					$documenthash[$word] = array(array($id, $count, 0));
				} else {
					$documenthash[$word][] = array($id, $count, 0);
				}
			}
		}

		foreach ($documenthash as $key => $value) {
			usort($value, array($this->ranker, 'rankDocuments'));
			$this->index->storeDocuments($key, $value);
		}

		return true;
	}

	/*doc*/
	public function _concordance(array $document) {
		if (!is_array($document)) {
			return array();
		}
		$con = array();
		foreach ($document as $word) {
			if (array_key_exists($word, $con)) {
				$con[$word] = $con[$word] + 1;
			} else {
				$con[$word] = 1;
			}
		}
		return $con;
	}

	/*doc*/
	public function _cleanDocument($document) {
		if (!is_string($document)) {
			return array();
		}
		$cleandocument = strip_tags(strtolower($document));
		$cleandocument = preg_replace('/\W/i', ' ', $cleandocument);
		$cleandocument = preg_replace('/\s\s+/', ' ', $cleandocument);
		if ($cleandocument != '') {
			return explode(' ', trim($cleandocument));
		}
		return array();
	}

}

?>
