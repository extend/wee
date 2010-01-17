<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Web:Extend - QUnit Test Suite</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
	<link type="text/css" rel="stylesheet" href="http://github.com/jquery/qunit/raw/master/qunit/qunit.css" media="screen"/>
	<script type="text/javascript" src="http://github.com/jquery/qunit/raw/master/qunit/qunit.js"></script>

	<script type="text/javascript">
function run(t) {
	var result = $.ajax({url:t,async:false}).responseText;
	ok(result == 'success', result);
}

function batch(n, t) {
	for (var i = 1; i <= n; i++)
		run(t + '?step=' + i);
}

$(function() {
	module('Cookies');
	test('set', function() {batch(3, 'cookies/set.php');});
	test('delete', function() {batch(2, 'cookies/delete.php');});
	test('apppath', function() {batch(1, 'cookies/apppath.php');});
	test('path', function() {batch(1, 'cookies/path.php');});

	module('Files');
	test('upload', function() {batch(2, 'files/upload.php');});

	module('Session');
	test('bad name', function() {batch(3, 'session/badname.php');});
	test('bad token', function() {batch(3, 'session/badtoken.php');});
	test('set values', function() {batch(3, 'session/set.php');});
	test('clear', function() {batch(4, 'session/clear.php');});

	module('Session (storage: database table)');
	test('set values', function() {batch(4, 'session/dbtableset.php');});
	test('clear', function() {batch(5, 'session/dbtableclear.php');});

	module('CLI');
<?php
define('DEBUG', 1);
define('ALLOW_INCLUSION', 1);
define('ROOT_PATH', '../../');
require(ROOT_PATH . 'wee/wee.php');

// Clean up the tmp directory

exec('rm -rf ' . ROOT_PATH . 'app/tmp/*');

// Run the test suite

$o = new weeTestSuite('../tests/');

$aTests = $o->toArray();
unset($aTests[getcwd() . '/../tests/maketests.php']);
$aTests = array_keys($aTests);

foreach ($aTests as $i => $sPath):?>
	test('<?php echo substr($sPath, strlen(getcwd() . '/../tests/'))?>', function() {run('cli.php?t=<?php echo $i?>');});
<?php endforeach?>
});
	</script>
</head>

<body>
	<h1 id="qunit-header">Web:Extend - QUnit Test Suite</h1>
	<h2 id="qunit-banner"></h2>
	<h2 id="qunit-userAgent"></h2>
	<ol id="qunit-tests"></ol>
</body>
</html>
