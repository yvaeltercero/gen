<html>
<head>
<style>
body {
	font-family:"futura-pt-condensed","trebuchet ms";
}
p {
	margin:0px;
}
li {
	margin-top:20px;
}
</style>
</head>
<body>
<form method="get">
<input type="input" name="q" value="<?php echo $_GET['q']; ?>" />
<input type="submit" value="Search" />
</form>
<?php
set_time_limit(0);
define('INDEXLOCATION',dirname(__FILE__).'/index/');
define('DOCUMENTLOCATION',dirname(__FILE__).'/documents/');

include_once('./classes/coolindexer.class.php');
include_once('./classes/coolsearch.class.php');
include_once('./classes/coolindex.class.php');
include_once('./classes/cooldocumentstore.class.php');
include_once('./classes/dummyranker.class.php');

$index = new coolindex();
$docstore = new cooldocumentstore();
$ranker = new dummyranker();
$indexer = new coolindexer($index,$docstore,$ranker);
$search = new coolsearch($index,$docstore,$ranker);


echo '<ul style="list-style:none;">';
foreach($search->dosearch($_GET['q']) as $result) {
	?>
	<li>
		<a href="<?php echo $result[0][0]; ?>"><?php echo $result[0][1]; ?></a><br>
		<a style="color:#093; text-decoration:none;" href="<?php echo $result[0][0]; ?>"><?php echo $result[0][0]; ?></a>
		<p><?php echo $result[0][2]; ?></p>
	</li>
	<?php
}
echo '</ul>';
?>
</body>
</html>
