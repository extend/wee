<?php

// Connect

try
{
	$oDb = new weeOracleDatabase(array(
		'dsn'		=> 'oci:dbname=kaboom',
		'user'		=> 'wee',
		'password'	=> 'wee',
	));
}
catch (Exception $o)
{
	$this->skip();
}
