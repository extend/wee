<?php

// Connect

try
{
	$oDb = new weeMySQLiDatabase(array(
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
