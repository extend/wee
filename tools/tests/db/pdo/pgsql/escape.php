<?php

require('connect.php.inc');

// Test the method weePDODatabase::escape

$this->isEqual("'egg'", $oDb->escape('egg'),
	_WT("Escaping of the string 'egg' is wrong."));
$this->isEqual("'123'", $oDb->escape(123),
	_WT('Escaping of the integer 123 is wrong.'));

$sFormerLocale = setlocale(LC_NUMERIC, 'C');

try {
	$this->isEqual("'7.5'", $oDb->escape(7.5),
		_WT('weePgSQLDatabase::escape does not return the expected escaped float when the locale is "C".'));

	setlocale(LC_NUMERIC, 'fr_FR');

	$this->isEqual("'7.5'", $oDb->escape(7.5),
		_WT('weePgSQLDatabase::escape does not return the expected escaped float when the locale is "fr_FR".'));
}
catch (Exception $oException) {}

setlocale(LC_NUMERIC, $sFormerLocale);
if (isset($oException))
	throw $oException;

$this->isEqual("'that''s all folks!'", $oDb->escape("that's all folks!"),
	_WT('Escaping of the string "that\'s all folks" is wrong.'));

$this->isEqual('null', $oDb->escape(null),
	_WT('null is not properly escaped.'));

// see http://wee.extend.ws/ticket/73
try {
	$this->isEqual("'1'", $oDb->escape(true),
		_WT('escape does not correctly escape true values.'));

	$this->isEqual("'0'", $oDb->escape(false),
		_WT('escape does not correctly escape false values.'));
} catch (ErrorException $e) {
	$this->fail(_WT('escape should not trigger an error when trying to escape boolean values.'));
}

// Test the method weePDODatabase::escapeIdent

$this->isEqual('"egg"', $oDb->escapeIdent('egg'),
	_WT('escapeIdent does not properly escape the identifier "egg".'));

$this->isEqual('"that""s all folks!"', $oDb->escapeIdent('that"s all folks!'),
	_WT('escapeIdent does not properly escape the identifier \'that"s all folks!\'.'));

try {
	$oDb->escapeIdent('');
	$this->fail(_WT('escapeIdent does not throw an InvalidArgumentException when the identifier is empty.'));
} catch(InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent("\0");
	$this->fail(_WT('escapeIdent does not throw an InvalidArgumentException when the identifier contains a NUL character.'));
} catch(InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent(str_repeat('w', 64));
	$this->fail(_WT('escapeIdent does not throw an InvalidArgumentException when the identifier is longer than 63 characters.'));
} catch(InvalidArgumentException $e) {}
