<?php

require('connect.php.inc');

// Test the method weePDODatabase::escape

$this->isEqual("'egg'", $oDb->escape('egg'),
	"Escaping of the string 'egg' is wrong.");
$this->isEqual("'123'", $oDb->escape(123),
	'Escaping of the integer 123 is wrong.');
$this->isEqual("'7.5'", $oDb->escape(7.5),
	'Escaping of the float 7.5 is wrong.');
$this->isEqual("'7.5'", $oDb->escape(7.50),
	'Escaping of the float 7.50 is wrong.');

$this->isEqual("'that\'s all folks!'", $oDb->escape("that's all folks!"),
	'Escaping of the string "that\'s all folks" is wrong.');

$this->isEqual('null', $oDb->escape(null),
	'null is not properly escaped.');

// Test the method weePDODatabase::escapeIdent

$this->isEqual('"egg"', $oDb->escapeIdent('egg'),
	'escapeIdent does not properly escape the identifier "egg".');

try {
	$oDb->escapeIdent('that"s all folks!');
	$this->fail(_WT('weePDODatabase::escapeIdent does not throw an InvalidArgumentException when the identifier contains double quotes.'));
} catch (InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent('');
	$this->fail('weePDODatabase::escapeIdent does not throw an InvalidArgumentException when the identifier is empty.');
} catch(InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent(str_repeat('w', 31));
	$this->fail('weePDODatabase::escapeIdent does not throw an InvalidArgumentException when the identifier is longer than 30 characters.');
} catch(InvalidArgumentException $e) {}
