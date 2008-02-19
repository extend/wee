<?php

// Connect

try
{
  $oDb = new weePgSQLDatabase(array(
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
