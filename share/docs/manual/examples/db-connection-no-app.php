<?php

$aDbSettings = array(
	'host'		=> 'localhost',
	'user'		=> 'myusername',
	'password'	=> 'mypassword',
	'dbname'	=> 'mydb',
);

$oDb = new weePgSQLDatabase($aDbSettings);
