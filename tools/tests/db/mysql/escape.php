<?php

require('connect.php.inc');

// Test the method weeMySQLDatabase::escape

$this->isEqual("'egg'", $oDb->escape('egg'),
	"Escaping of the string 'egg' is wrong.");
$this->isEqual("'123'", $oDb->escape(123),
	'Escaping of the integer 123 is wrong.');
$this->isEqual("'7.5'", $oDb->escape(7.5),
	'Escaping of the float 7.5 is wrong.');
$this->isEqual("'7.5'", $oDb->escape(7.50),
	'Escaping of the float 7.50 is wrong.');

$this->isEqual("'that\\'s all folks!'", $oDb->escape("that's all folks!"),
	'Escaping of the string "that\'s all folks" is wrong.');

$this->isEqual('null', $oDb->escape(null),
	'null is not properly escaped.');

// Test the method weeMySQLDatabase::escapeIdent

$this->isEqual('`egg`', $oDb->escapeIdent('egg'),
	'escapeIdent does not properly escape the identifier "egg".');

$this->isEqual('`that``s all folks!`', $oDb->escapeIdent('that`s all folks!'),
	'escapeIdent does not properly escape the identifier "that`s all folks!".');

try {
	$oDb->escapeIdent('');
	$this->fail('escapeIdent does not throw an InvalidArgumentException when the identifier is empty.');
} catch(InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent("\0");
	$this->fail('escapeIdent does not throw an InvalidArgumentException when the identifier contains a NUL character.');
} catch(InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent(chr(255));
	$this->fail('escapeIdent does not throw an InvalidArgumentException when the identifier contains a byte with a value of 255.');
} catch(InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent('wee ');
	$this->fail('escapeIdent does not throw an InvalidArgumentException when the identifier end with space characters.');
} catch(InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent(str_repeat('w', 65));
	$this->fail('escapeIdent does not throw an InvalidArgumentException when the identifier is longer than 64 characters.');
} catch(InvalidArgumentException $e) {}
