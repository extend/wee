<?php

require(dirname(__FILE__) . '/../../../oracle/connect.php.inc');
$oMeta = $oDb->meta();

$aArgs	= array('max' => 42);
$aTypes	= array('LONG' => array()) + array_combine(
	array('CHAR', 'VARCHAR2', 'NCHAR', 'NVARCHAR2'),
	array_pad(array(), 4, $aArgs)
);

$oDb->query('CREATE TABLE "dbmeta" (
	"a" LONG,
	"b" CHAR(42),
	"c" VARCHAR2(42),
	"d" NCHAR(42),
	"e" NVARCHAR2(42)
)');

try {
	$oTable = $oMeta->table('dbmeta');

	$i = ord('a');
	foreach ($aTypes as $sType => $aArgs) {
		$oColumn = $oTable->column(chr($i++));

		$this->isTrue($oColumn->hasValidator(),
			sprintf(_WT('weeOracleDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), $sType));

		try {
			$o = $oColumn->getValidator();
		} catch (UnhandledTypeException $e) {
			$this->fail(sprintf(_WT('weeOracleDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the column is "%s".'), $sType));
		}

		$this->isInstanceOf($o, 'weeStringValidator',
			sprintf(_WT('weeOracleDbMetaColumn::getValidator should return an instance of weeStringValidator when the type of the column is "%s".'), $sType));

		$this->isEqual($aArgs, $o->getArgs(),
			sprintf(_WT('The arguments of the "%s" validator are not correct.'), $sType));
	}
} catch (Exception $eException) {}

$oDb->query('DROP TABLE "dbmeta"');
if (isset($eException))
	throw $eException;
