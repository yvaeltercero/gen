<?php
/*
** Ranker: classification of documents according to appearances
** The document with the most appearances has more releavance
*/
interface iranker{
	public function rankDocuments($document,$document2);
}

?>
