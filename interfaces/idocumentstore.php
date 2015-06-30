<?php
/*
** Document Store: Structural definition of the documents
** retrieved by the spidey crawler
*/
interface idocumentstore {
	public function storeDocument(array $document);
	public function getDocument($documentid);
	public function clearDocuments();
}

?>
