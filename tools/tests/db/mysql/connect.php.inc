<?php

// Connect

try
{
	$oDb = new weeMySQLDatabase(array(
		'host'		=> 'localhost',
		'user'		=> 'wee_tests',
		'password'	=> 'wee_tests',
		'dbname'	=> 'wee_tests',
	));
}
catch (DatabaseException $o)
{
	$this->skip();
}
