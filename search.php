<form method="get">
<input type="input" name="q" value="<?php echo $_GET['q'];?>"/>
<input type="submit" />
</form>
<?php
define('INDEXLOCATION', dirname(__FILE__) . '/index/');
define('DOCUMENTLOCATION', dirname(__FILE__) . '/documents/');

include_once './classes/indexer.class.php';
include_once './classes/searcher.class.php';
include_once './classes/index.class.php';
include_once './classes/documentstore.class.php';
//include_once './classes/naieveranker.class.php';

$index = new index();
$docstore = new documentstore();
$indexer = new indexer($index, $docstore);
//$ranker = new naieveranker();
$search = new searcher($index, $docstore);

echo '<ul>';
foreach ($search->dosearch($_GET['q']) as $result) {
	echo '<li>' . $result[0] . '</li>';
}
echo '</ul>';
?>
