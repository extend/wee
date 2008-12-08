<?php

// Connect

try
{
	$oDb = new weePDODatabase(array(
		'dsn'		=> 'mysql:host=localhost;dbname=wee_tests',
		'user'		=> 'wee',
		'password'	=> 'wee',
	));
}
catch (Exception $o)
{
	$this->skip();
}
