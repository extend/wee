<?php

require(dirname(__FILE__) . '/../../../oracle/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('CREATE TABLE "dbmeta" (
	"a" NUMBER,
	"b" NUMBER(*, 0),
	"c" NUMBER(*, -2),
	"d" NUMBER(*, 2),
	"e" BINARY_FLOAT,
	"f" BINARY_DOUBLE
)');

try {
	$oTable = $oMeta->table('dbmeta');

	$i = ord('a');
	foreach (array(null, 0, -2, 2) as $iScale) {
		$oColumn	= $oTable->column(chr($i++));
		$sType		= $iScale !== null ? 'NUMBER(*, ' . $iScale . ')' : 'NUMBER';

		$this->isTrue($oColumn->hasValidator(),
			sprintf(_WT('weeOracleDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), $sType));

		try {
			$o = $oColumn->getValidator();
		} catch (UnhandledTypeException $e) {
			$this->fail(sprintf(_WT('weeOracleDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "%s".'), $sType));
		}

		$this->isInstanceOf($o, 'weeBigNumberValidator',
			sprintf(_WT('weeOracleDbMetaColumn::getValidator should return an instance of weeBigNumberValidator when the type of the column is "%s".'), $sType));

		if ($iScale > 0)
			$this->isEqual(array('format' => 'float'), $o->getArgs(),
				_WT('The validator should accept floats when the type of the column is "NUMBER" and the scale is greater than 0.'));
		else
			$this->isEqual(array('format' => 'int'), $o->getArgs(),
				_WT('The validator should accept integers when the type of the column is "NUMBER" and the scale is less than or equal to 0.'));
	}

	foreach (array('BINARY_FLOAT', 'BINARY_DOUBLE') as $sType) {
		$oColumn	= $oTable->column(chr($i++));

		$this->isTrue($oColumn->hasValidator(),
			sprintf(_WT('weeOracleDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), $sType));

		try {
			$o = $oColumn->getValidator();
		} catch (UnhandledTypeException $e) {
			$this->fail(sprintf(_WT('weeOracleDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "%s".'), $sType));
		}

		$this->isInstanceOf($o, 'weeBigNumberValidator',
			sprintf(_WT('weeOracleDbMetaColumn::getValidator should return an instance of weeBigNumberValidator when the type of the column is "%s".'), $sType));

		$this->isEqual(array('format' => 'float'), $o->getArgs(),
			sprintf(_WT('The validator should accept floats when the type of the column is "%s".'), $sType));
	}
}
catch (Exception $eException) {}

$oDb->query('DROP TABLE "dbmeta"');
if (isset($eException))
	throw $eException;
