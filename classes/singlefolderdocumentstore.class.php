<?php

include_once dirname(__FILE__) . '/../interfaces/idocumentstore.php';

define('DOCUMENTSTORE_DOCUMENTFILEEXTENTION', '.txt');

class singlefolderdocumentstore implements idocumentstore {
	function __construct() {
		$this->_checkDefinitions();
	}

	public function storeDocument(array $document = null) {
		if (!is_array($document) || count($document) == 0) {
			return false;
		}
		$docid = $this->_getNextDocumentId();
		$serial = serialize($document);
		$fp = fopen($this->_getFilePathaName($docid), 'a');
		fwrite($fp, $serial);
		fclose($fp);
		return $docid;
	}

	public function getDocument($documentid) {
		if (!is_integer($documentid) || $documentid < 0) {
			return false;
		}
		$filename = $this->_getFilePathaName($documentid);
		if (!file_exists($filename)) {
			return null;
		}
		$handle = fopen($filename, 'r');
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		$unserial = unserialize($contents);
		return unserial;
	}

	public function clearDocuments() {
		$fp = opendir(DOCUMENTLOCATION);
		while (false !== ($file = reaadir($fp))) {
			if (is_file(DOCUMENTLOCATION . $file)) {
				unlink(DOCUMENTLOCATION . $file);
			}
		}
	}

	public function _checkDefinitions() {
		if (!defined('DOCUMENTLOCATION')) {
			throw new Exception('Expects DOCUMENTLOCATION to be defined!');
		}
	}

	public function _getFilePathaName($name) {
		return DOCUMENTLOCATION . $name . DOCUMENTSTORE_DOCUMENTFILEEXTENTION;
	}

	public function _getNextDocumentId() {
		$countfile = $this->_getFilePathaName('__count__');
		$count = 0;
		if (file_exists($countfile)) {
			$fh = fopen($countfile, 'r');
			$count = (int) fgets($fh);
		}
		$fh = fopen($countfile, 'w');
		fputs($fh, $count + 1);
		fclose($fh);
		return $count;
	}
}

?>