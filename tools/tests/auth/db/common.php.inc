<?php

if (!defined('MAGIC_STRING'))
	define('MAGIC_STRING', 'Our dreams are lost in the flow of time, still we are looking for the future in this wired world...');

$oDb->query('
	CREATE TEMPORARY TABLE auth (
		user_id SERIAL NOT NULL,
		user_email CHARACTER VARYING(255),
		user_passwd CHARACTER(32),
		user_randomint INT,
		user_randomstr CHARACTER VARYING(50)
	);
');

// Insert values into the test table

$aInsertValues = array(
	array(
		'user_email'		=> 'reimu@example.com',
		'user_passwd'		=> 'nekomiko',
		'user_randomint'	=> rand(),
		'user_randomstr'	=> md5(rand()),
	),
	array(
		'user_email'		=> 'marisa@example.com',
		'user_passwd'		=> 'witch',
		'user_randomint'	=> rand(),
		'user_randomstr'	=> md5(rand()),
	),
	array(
		'user_email'		=> 'yukari@example.com',
		'user_passwd'		=> 'border',
		'user_randomint'	=> rand(),
		'user_randomstr'	=> md5(rand()),
	),
	array(
		'user_email'		=> 'yuyuko@example.com',
		'user_passwd'		=> 'ghosts',
		'user_randomint'	=> rand(),
		'user_randomstr'	=> md5(rand()),
	),
	array(
		'user_email'		=> 'flandre@example.com',
		'user_passwd'		=> 'evilgirl42',
		'user_randomint'	=> rand(),
		'user_randomstr'	=> md5(rand()),
	),
	array(
		'user_email'		=> 'remilia@example.com',
		'user_passwd'		=> 'evilsister',
		'user_randomint'	=> rand(),
		'user_randomstr'	=> md5(rand()),
	),
);

foreach ($aInsertValues as $aRow)
	$oDb->query('INSERT INTO auth (user_email, user_passwd, user_randomint, user_randomstr)
		VALUES (:user_email, MD5(:user_passwd), :user_randomint, :user_randomstr)', $aRow);

// Test the authentication

$oAuth = new weeAuthDbTable(array(
	'db'				=> $oDb,
	'table'				=> 'auth',
	'identifier_field'	=> 'user_email',
	'password_field'	=> 'user_passwd',
));

try {
	$aData = $oAuth->authenticate(array(
		'identifier'	=> $aInsertValues[0]['user_email'],
		'password'		=> $aInsertValues[4]['user_passwd'],
	));

	$this->fail('The credentials provided were incorrect and yet the authentication succeeded.');
} catch (AuthenticationException $e) {
}

try {
	$aData = $oAuth->authenticate(array(
		'identifier'	=> $aInsertValues[3]['user_email'],
		'password'		=> $aInsertValues[3]['user_passwd'],
	));

	$this->isTrue(empty($aData['user_passwd']), 'The password has not been removed from the data.');

	unset($aData['user_id'], $aInsertValues[3]['user_passwd']);
	$this->isEqual($aData, $aInsertValues[3], 'The data returned by the authentication is incorrect.');
} catch (AuthenticationException $e) {
	$this->fail('The credentials provided were correct and yet the authentication failed.');
}

// Test the hash based authentication

try {
	$aData = $oAuth->authenticateHash(array(
		'identifier'	=> $oAuth->hash($aInsertValues[2]['user_email']),
		'password'		=> $oAuth->hash('failfailfailfailfailfailfailfail'),
	));

	$this->fail('The hashed credentials provided were incorrect and yet the authentication succeeded.');
} catch (AuthenticationException $e) {
}

try {
	$aData = $oAuth->authenticateHash(array(
		'identifier'	=> $aInsertValues[5]['user_email'],
		'password'		=> $oAuth->hash($aInsertValues[5]['user_passwd']),
	));

	$this->isTrue(empty($aData['user_passwd']), 'The password has not been removed from the data.');

	unset($aData['user_id'], $aInsertValues[5]['user_passwd']);
	$this->isEqual($aData, $aInsertValues[5], 'The data returned by the authentication is incorrect.');
} catch (AuthenticationException $e) {
	$this->fail('The hashed credentials provided were correct and yet the authentication failed.');
}