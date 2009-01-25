<?php

isset($_GET['t']) or die('Argument missing.');

define('DEBUG', 1);
define('ALLOW_INCLUSION', 1);
define('ROOT_PATH', '../../');
require(ROOT_PATH . 'wee/wee.php');

$o = new weeTestSuite('../tests/');

$aTests = $o->toArray();
unset($aTests[getcwd() . '/../tests/maketests.php']);
$aTests = array_keys($aTests);

try {
	$oTest = new weeUnitTestCase($aTests[$_GET['t']]);
	$oTest->run();

	echo 'success';
} catch (SkipTestException $o) {
	echo 'skip';
} catch (Exception $o) {
	echo $o;
}
