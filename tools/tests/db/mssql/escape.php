<?php

if (!isset($oDb))
	require('connect.php.inc');

// Test the method weeMSSQLDatabase::escape

$this->isEqual("'egg'", $oDb->escape('egg'),
	_WT("Escaping of the string 'egg' is wrong."));
$this->isEqual("'123'", $oDb->escape(123),
	_WT('Escaping of the integer 123 is wrong.'));
$this->isEqual("'7.5'", $oDb->escape(7.5),
	_WT('Escaping of the float 7.5 is wrong.'));
$this->isEqual("'7.5'", $oDb->escape(7.50),
	_WT('Escaping of the float 7.50 is wrong.'));

$this->isEqual("'that''s all folks!'", $oDb->escape("that's all folks!"),
	_WT('Escaping of the string "that\'s all folks" is wrong.'));

$this->isEqual('null', $oDb->escape(null),
	_WT('null is not properly escaped.'));

// Test the method weeMSSQLDatabase::escapeIdent

$this->isEqual('[egg]', $oDb->escapeIdent('egg'),
	_WT('escapeIdent does not properly escape the identifier "egg".'));

$this->isEqual('[abc[]]def]', $oDb->escapeIdent('abc[]def'),
	_WT('escapeIdent does not properly escape the identifier "abc[]def".'));

try {
	$oDb->escapeIdent('');
	$this->fail(_WT('weeMSSQLDatabase::escapeIdent does not throw an InvalidArgumentException when the identifier is empty.'));
} catch(InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent(str_repeat('w', 129));
	$this->fail(_WT('weeMSSQLDatabase::escapeIdent does not throw an InvalidArgumentException when the identifier is longer than 30 characters.'));
} catch(InvalidArgumentException $e) {}
