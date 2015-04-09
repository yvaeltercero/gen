<form method="get">
<input type="input" name="q" value="<?php echo $_GET['q'];?>"/>
<input type="submit" />
</form>
<?php
define('INDEXLOCATION', dirname(__FILE__) . '/index/');
define('DOCUMENTLOCATION', dirname(__FILE__) . '/documents/');

include_once './classes/naieveindexer.class.php';
include_once './classes/naievesearch.class.php';
include_once './classes/singlefolderindex.class.php';
include_once './classes/singlefolderdocumentstore.class.php';
//include_once './classes/naieveranker.class.php';

$index = new singlefolderindex();
$docstore = new singlefolderdocumentstore();
$indexer = new naieveindexer($index, $docstore);
//$ranker = new naieveranker();
$search = new naievesearch($index, $docstore);

echo '<ul>';
foreach ($search->dosearch($_GET['q']) as $result) {
	echo '<li>' . $result[0] . '</li>';
}
echo '</ul>';
?>
