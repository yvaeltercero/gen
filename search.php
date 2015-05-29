<form method="get">
<input type="input" name="q" value="<?php echo $_GET['q'];?>"/>
<input type="submit" />
</form>
<?php
define('INDEXLOCATION', dirname(__FILE__) . '/index/');
define('DOCUMENTLOCATION', dirname(__FILE__) . '/documents/');

include_once './classes/coolindexer.class.php';
include_once './classes/coolsearch.class.php';
include_once './classes/coolindex.class.php';
include_once './classes/cooldocumentstore.class.php';
include_once './classes/ranker.class.php';

$index = new coolindex();
$docstore = new cooldocumentstore();
$ranker = new ranker();
$indexer = new coolindexer($index, $docstore, $ranker);
$search = new coolsearch($index, $docstore,$ranker);

echo '<ul>';
foreach ($search->dosearch($_GET['q']) as $result) {
	echo '<li>' . $result[0] . '</li>';
}
echo '</ul>';
?>
