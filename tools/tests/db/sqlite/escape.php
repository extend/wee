<?php

if (!isset($oDb))
	require('connect.php.inc');

// weeDatabase::escape

$this->isEqual("'egg'", $oDb->escape('egg'),
	_WT('weeDatabase::escape does not return the expected escaped string.'));

$this->isEqual("'123'", $oDb->escape(123),
	_WT('weeDatabase::escape does not return the expected escaped integer.'));

$sFormerLocale = setlocale(LC_NUMERIC, 'C');

try {
	$this->isEqual("'7.5'", $oDb->escape(7.5),
		_WT('weeDatabase::escape does not return the expected escaped float when the locale is "C".'));

	setlocale(LC_NUMERIC, 'fr_FR');

	$this->isEqual("'7.5'", $oDb->escape(7.5),
		_WT('weeDatabase::escape does not return the expected escaped float when the locale is "fr_FR".'));
}
catch (Exception $oException) {}

setlocale(LC_NUMERIC, $sFormerLocale);
if (isset($oException))
	throw $oException;

$this->isEqual("'that''s all folks!'", $oDb->escape("that's all folks!"),
	_WT('weeDatabase::escape does not return the expected escaped string when it contains single quotes.'));

$this->isEqual('null', $oDb->escape(null),
	_WT('weeDatabase::escape does not return the expected escaped null value.'));

// weeDatabase::escapeIdent

$this->isEqual('[egg]', $oDb->escapeIdent('egg'),
	_WT('escapeIdent does not properly escape the identifier "egg".'));

try {
	$oDb->escapeIdent('[');
	$this->fail(sprintf(_WT('weeDatabase::escapeIdent does not throw an InvalidArgumentException when the identifier contains "%s".'), '['));
} catch (InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent(']');
	$this->fail(sprintf(_WT('weeDatabase::escapeIdent does not throw an InvalidArgumentException when the identifier contains "%s".'), ']'));
} catch (InvalidArgumentException $e) {}

try {
	$oDb->escapeIdent('');
	$this->fail(_WT('weeDatabase::escapeIdent does not throw an InvalidArgumentException when the identifier is empty.'));
} catch(InvalidArgumentException $e) {}
