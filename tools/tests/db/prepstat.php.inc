<?php

$this->isFalse(empty($oDb), sprintf(_WT('The file %s requires a database object $oDb in the current scope before it is included.'), __FILE__));

$aInsertValues = array(
	array(
		'ps_integer'	=> 42,
		'ps_varchar'	=> "The world's most advanced open source database.",
		'ps_timestamp'	=> '2004-10-19 10:23:54',
		'ps_boolean'	=> false
	),
	array(
		'ps_integer'	=> 4242,
		'ps_varchar'	=> "The universe's most advanced open source database.",
		'ps_timestamp'	=> '2005-10-19 15:59:00',
		'ps_boolean'	=> true
	),
	array(
		'ps_integer'	=> 424242,
		'ps_varchar'	=> "The sea's most advanced open source database.",
		'ps_timestamp'	=> '2006-10-19 03:01:47',
		'ps_boolean'	=> false
	),
	array(
		'ps_integer'	=> -1000,
		'ps_varchar'	=> "The cheese's most advanced open source database.",
		'ps_timestamp'	=> '2007-10-19 20:21:22',
		'ps_boolean'	=> false
	),
);

// Create the test table

$oDb->query('
	CREATE TABLE prepstat (
		ps_id INTEGER PRIMARY KEY,
		ps_integer INTEGER,
		ps_varchar CHARACTER VARYING(50),
		ps_timestamp ' . ($oDb->is('mssql') ? 'datetime2' : 'TIMESTAMP') . '(0),
		ps_boolean ' . ($oDb->is('oracle') || $oDb->is('mssql') ? 'INTEGER' : 'BOOLEAN') . '
	)
');

if ($oDb->is('oracle'))
	$oDb->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");

try {
	$mNumAffectedRows = $oDb->numAffectedRows();

	// see http://wee.extend.ws/ticket/73
	$oBooleanStat = $oDb->prepare('SELECT * FROM prepstat WHERE ps_boolean = :bool');
	try {
		$oBooleanStat->bind(array('bool' => true))->execute();
		$oBooleanStat->bind(array('bool' => false))->execute();
	} catch (DatabaseException $e) {
		$this->fail(_WT('weeDatabaseStatement should not trigger an error when using booleans.'));
	}

	$oInsertStat = $oDb->prepare('
		INSERT INTO prepstat
		VALUES (:ps_id, :ps_integer, :ps_varchar, :ps_timestamp, :ps_boolean)
	');

	foreach ($aInsertValues as $i => $aRow)
		$oInsertStat->bind($aRow + array('ps_id' => $i))->execute();

	// see http://wee.extend.ws/ticket/74
	$this->isEqual($mNumAffectedRows, $oDb->numAffectedRows(),
		_WT('weeDatabaseStatement::execute should not modify the value returned by weeDatabase::numAffectedRows.'));

	// Get the number of rows

	$iNumResults = count($oDb->prepare('
		SELECT * FROM prepstat
	')->execute());

	$this->isEqual(4, $iNumResults,
		_WT('"SELECT * FROM prepstat" should return 4 rows.'));

	// Get a specific row

	$aImplicitTest = $oDb->prepare('
		SELECT ps_integer FROM prepstat WHERE ps_boolean = :bool
	')->bind('bool', true)->execute()->fetch();

	$this->isEqual(4242, reset($aImplicitTest),
		_WT('"SELECT ps_integer FROM prepstat WHERE ps_boolean=1" should return 4242.'));

	// weeStatement::numAffectedRows

	$oDeleteStat = $oDb->prepare('DELETE FROM prepstat WHERE 1 = 1');
	$oDeleteStat->execute();
	$this->isEqual(count($aInsertValues), $oDeleteStat->numAffectedRows(),
		_WT('weeStatement::numAffectedRows does not correctly return the number of affected rows.'));
} catch (Exception $oException) {}

$oDb->query('DROP TABLE prepstat');
if (isset($oException))
	throw $oException;
