<?php

if ($argc != 2)
{
	echo "usage: php makeapi.php output_path\n";
	return -1;
}

define('DEBUG', 1);
define('ALLOW_INCLUSION', 1);
require('wee/wee.php');

$o = new weeDocumentor;
file_put_contents(
	$argv[1] . 'api.xml',
	$o	->docClassFromPath('wee')
		->docFunc('fire')
		->docFunc('burn')
		->docFunc('array_value')
		->toString()
);

echo $argv[1] . 'api.xml created successfully.';

?>

