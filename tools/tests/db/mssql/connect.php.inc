<?php

try {
	$oDb = new weeMSSQLDatabase(array(
		'host'		=> 'kaboom',
		'user'		=> 'wee',
		'password'	=> 'wee',
		'dbname'	=> 'wee_tests',
	));
} catch (Exception $e) {
	$this->skip();
}
