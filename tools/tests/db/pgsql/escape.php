<?php

require('connect.php.inc');

// Test the method weePgSQLDatabase::escape

$this->isEqual("'egg'", $oDb->escape('egg'),
	"Escaping of the string 'egg' is wrong.");
$this->isEqual("'123'", $oDb->escape(123),
	'Escaping of the integer 123 is wrong.');
$this->isEqual("'7.5'", $oDb->escape(7.5),
	'Escaping of the float 7.5 is wrong.');
$this->isEqual("'7.5'", $oDb->escape(7.50),
	'Escaping of the float 7.50 is wrong.');

$this->isEqual("'that''s all folks!'", $oDb->escape("that's all folks!"),
	'Escaping of the string "that\'s all folks" is wrong.');
