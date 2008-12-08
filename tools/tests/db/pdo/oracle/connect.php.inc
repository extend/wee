<?php

// Connect

try
{
	$oDb = new weeOracleDatabase(array(
		'dsn'		=> 'oci:bname=kaboom',
		'user'		=> 'wee',
		'password'	=> 'wee',
	));
}
catch (Exception $o)
{
	$this->skip();
}
