<html>
	<head>
	<!-- Bootstrap -->
	</head>
	<!-- Search bar and trigger button -->
	<body>
		<form method="get">
			<input type="input" name="q" value="<?php echo $_GET['q']; ?>" />
			<input type="submit" value="Search" />
		</form>
	<?php
		// <time-out>
		set_time_limit(0);
		// constants: PATH to the document and index repos
		define('INDEXLOCATION',dirname(__FILE__).'/index/');
		define('DOCUMENTLOCATION',dirname(__FILE__).'/documents/');
		// dependencies
		include_once('./classes/coolindexer.class.php');
		include_once('./classes/coolsearch.class.php');
		include_once('./classes/coolindex.class.php');
		include_once('./classes/cooldocumentstore.class.php');
		include_once('./classes/dummyranker.class.php');
		// vars
		$index = new coolindex();
		$docstore = new cooldocumentstore();
		$ranker = new dummyranker();
		$search = new coolsearch($index,$docstore,$ranker);
		// Result list
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
