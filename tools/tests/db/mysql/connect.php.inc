<?php

// Connect

try
{
	$oDb = new weeMySQLDatabase(array(
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
