<?php

// Connect

try
{
	$oDb = new weePDODatabase(array(
		'dsn'		=> 'pgsql:host=localhost;dbname=wee_tests',
		'user'		=> 'wee',
		'password'	=> 'wee'
	));
}
catch (Exception $o)
{
	$this->skip();
}
