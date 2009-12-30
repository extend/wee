<?php

// Connect

// If it skips and you have pdo_mysql installed, add the following
// to the 'dsn' with a correct path to your mysql.sock file:
// ;unix_socket=/var/run/mysqld/mysqld.sock

try {
	$oDb = new weePDODatabase(array(
		'dsn'		=> 'mysql:host=localhost;dbname=wee_tests',
		'user'		=> 'wee',
		'password'	=> 'wee',
	));
} catch (Exception $o) {
	$this->skip();
}
