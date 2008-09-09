<?php

// Connect

try
{
	$oDb = new weePgSQLDatabase(array(
		'host'		=> 'localhost',
		'user'		=> 'wee',
		'password'	=> 'wee',
		'dbname'	=> 'wee_tests',
	));
}
catch (Exception $o)
{
	$this->skip();
}
