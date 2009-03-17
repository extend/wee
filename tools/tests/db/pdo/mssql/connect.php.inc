<?php

try {
	$oDb = new weePDODatabase(array(
		'dsn'		=> 'dblib:host=kaboom;dbname=wee_tests',
		'user'		=> 'wee',
		'password'	=> 'wee',
	));
} catch (Exception $e) {
    echo $e, "\n";
	$this->skip();
}
