<?php

if (!defined('MAGIC_STRING'))
	define('MAGIC_STRING', 'Our dreams are lost in the flow of time, still we are looking for the future in this wired world...');

$oDb->query('
	CREATE TABLE ' . $oDb->escapeIdent('auth') . ' (
		' . $oDb->escapeIdent('user_id') . ' INTEGER PRIMARY KEY,
		' . $oDb->escapeIdent('user_email') . ' VARCHAR(255),
		' . $oDb->escapeIdent('user_passwd') . ' VARCHAR(40),
		' . $oDb->escapeIdent('user_randomint') . ' INTEGER,
		' . $oDb->escapeIdent('user_randomstr') . ' VARCHAR(50)
	)
');

try {
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

	foreach ($aInsertValues as $i => $aRow)
		$oDb->query('
			INSERT INTO ' . $oDb->escapeIdent('auth') . ' (
				' . $oDb->escapeIdent('user_id') . ',
				' . $oDb->escapeIdent('user_email') . ',
				' . $oDb->escapeIdent('user_passwd') . ',
				' . $oDb->escapeIdent('user_randomint') . ',
				' . $oDb->escapeIdent('user_randomstr') . '
			) VALUES (:id, :user_email, :hash, :user_randomint, :user_randomstr)
		', array('id' => $i, 'hash' => sha1($aRow['user_passwd'])) + $aRow);

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

		$this->fail(_WT('The credentials provided were incorrect and yet the authentication succeeded.'));
	} catch (AuthenticationException $e) {
	}

	try {
		$aData = $oAuth->authenticate(array(
			'identifier'	=> $aInsertValues[3]['user_email'],
			'password'		=> $aInsertValues[3]['user_passwd'],
		));

		$this->isTrue(empty($aData['user_passwd']), _WT('The password has not been removed from the data.'));

		unset($aData['user_id'], $aInsertValues[3]['user_passwd']);
		$this->isEqual($aInsertValues[3], $aData,
			_WT('The data returned by the authentication is incorrect.'));
	} catch (AuthenticationException $e) {
		$this->fail(_WT('The credentials provided were correct and yet the authentication failed.'));
	}

	// Test the hash based authentication

	try {
		$aData = $oAuth->authenticateHash(array(
			'identifier'	=> $oAuth->hash($aInsertValues[2]['user_email']),
			'password'		=> $oAuth->hash('failfailfailfailfailfailfailfail'),
		));

		$this->fail(_WT('The hashed credentials provided were incorrect and yet the authentication succeeded.'));
	} catch (AuthenticationException $e) {
	}

	try {
		$aData = $oAuth->authenticateHash(array(
			'identifier'	=> $aInsertValues[5]['user_email'],
			'password'		=> $oAuth->hash($aInsertValues[5]['user_passwd']),
		));

		$this->isTrue(empty($aData['user_passwd']), _WT('The password has not been removed from the data.'));

		unset($aData['user_id'], $aInsertValues[5]['user_passwd']);
		$this->isEqual($aInsertValues[5], $aData,
			_WT('The data returned by the authentication is incorrect.'));
	} catch (AuthenticationException $e) {
		$this->fail(_WT('The hashed credentials provided were correct and yet the authentication failed.'));
	}
} catch (Exception $eException) {}

$oDb->query('DROP TABLE ' . $oDb->escapeIdent('auth'));
if (isset($eException))
	throw $eException;
