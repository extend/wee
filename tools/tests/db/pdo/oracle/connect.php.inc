<?php

try {
	$oDb = new weePDODatabase(array(
		'dsn'		=> 'oci:dbname=kaboom',
		'user'		=> 'wee',
		'password'	=> 'wee',
	));
} catch (Exception $e) {
	$this->skip();
}
