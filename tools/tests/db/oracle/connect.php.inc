<?php

// Connect

try
{
	$oDb = new weeOracleDatabase(array(
		'dbname'	=> 'kaboom',
		'user'		=> 'wee',
		'password'	=> 'wee',
	));
}
catch (Exception $o)
{
	$this->skip();
}
