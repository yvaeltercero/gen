<?php

define('INDEXLOCATION',dirname(__FILE__).'/../index/');
define('DOCUMENTLOCATION',dirname(__FILE__).'/../documents/');

include_once(dirname(__FILE__).'/../classes/naieveindexer.class.php');
include_once(dirname(__FILE__).'/../classes/naievesearch.class.php');
include_once(dirname(__FILE__).'/../classes/singlefolderindex.class.php');
include_once(dirname(__FILE__).'/../classes/singlefolderdocumentstore.class.php');

$index = new singlefolderindex();
$docstore = new singlefolderdocumentstore();
$indexer = new naieveindexer($index,$docstore);
$search = new naievesearch($index,$docstore);

echo '<ul>';
foreach($search->dosearch($_GET['a']) as $result) {
    echo '<li>'.$result[0].'</li>';
}
    echo '</ul>';

?>
