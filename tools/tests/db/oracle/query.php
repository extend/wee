<?php

require('connect.php.inc');

// Create the test table

try {
	$oDb->query('
		CREATE GLOBAL TEMPORARY TABLE QUERY (
			Q_ID INTEGER PRIMARY KEY NOT NULL,
			Q_NAME CHARACTER VARYING(50),
			Q_QUANTITY INTEGER,
			Q_PRICE NUMERIC(12,2)
		)
	');
} catch (DatabaseException $e) {
	// This is expected to fail if the temporary table already exist
}

// Insert values into the test table

$aInsertValues = array(
	array(
		'Q_NAME'		=> 'Web:Extend Downloads',
		'Q_QUANTITY'	=> 8,
		'Q_PRICE'		=> 0,
	),
	array(
		'Q_NAME'		=> 'Eggs',
		'Q_QUANTITY'	=> 12,
		'Q_PRICE'		=> 6.99,
	),
	array(
		'Q_NAME'		=> 'Shoes',
		'Q_QUANTITY'	=> 3,
		'Q_PRICE'		=> 25.95,
	),
	array(
		'Q_NAME'		=> 'Songs',
		'Q_QUANTITY'	=> 1000000,
		'Q_PRICE'		=> 0.99,
	),
	array(
		'Q_NAME'		=> 'Years',
		'Q_QUANTITY'	=> 2008,
		'Q_PRICE'		=> 42,
	),
	array(
		'Q_NAME'		=> 'Xmas Trees',
		'Q_QUANTITY'	=> 1225,
		'Q_PRICE'		=> 13.50,
	),
	array(
		'Q_NAME'		=> 'Seven Dwarfs',
		'Q_QUANTITY'	=> 7,
		'Q_PRICE'		=> 123,
	),
	array(
		'Q_NAME'		=> 'Computers',
		'Q_QUANTITY'	=> 6,
		'Q_PRICE'		=> 2500,
	),
	array(
		'Q_NAME'		=> 'Girls',
		'Q_QUANTITY'	=> 0,
		'Q_PRICE'		=> 9999999999,
	),
	array(
		'Q_NAME'		=> 'Items',
		'Q_QUANTITY'	=> 10,
		'Q_PRICE'		=> 0.50,
	),
);

foreach ($aInsertValues as $i => $aRow)
	$oDb->query('INSERT INTO QUERY (q_id, Q_NAME, Q_QUANTITY, Q_PRICE)
		VALUES (' . ($i + 1) . ', :Q_NAME, :Q_QUANTITY, :Q_PRICE)', $aRow);

// Do various queries to check our data (and our query method)

$aRow = $oDb->query('SELECT COUNT(*) AS C FROM QUERY')->fetch();
$this->isEqual($aRow['C'], sizeof($aInsertValues),
	"The total number of rows in the table isn't matching the number of rows inserted.");

$aRow = $oDb->query('SELECT COUNT(*) AS C FROM QUERY WHERE Q_QUANTITY>=?', 10)->fetch();
$this->isEqual($aRow['C'], 5,
	'The number of rows with Q_QUANTITY>=10 is wrong.');

$aRow = $oDb->query('SELECT COUNT(*) AS C FROM QUERY WHERE Q_PRICE<?', 13.55)->fetch();
$this->isEqual($aRow['C'], 5,
	'The number of rows with Q_PRICE<13.55 is wrong.');

$aRow = $oDb->query('SELECT COUNT(*) AS C FROM QUERY WHERE Q_NAME=?', 'Cute Girls')->fetch();
$this->isEqual($aRow['C'], 0,
	'There is no cute girl in this table.');

$aRow = $oDb->query('SELECT COUNT(*) AS C FROM QUERY WHERE Q_QUANTITY>=? AND Q_PRICE<?', 10, 13.55)->fetch();
$this->isEqual($aRow['C'], 4,
	'The number of rows with Q_QUANTITY>=10 AND Q_PRICE<13.55 is wrong.');

$aRow = $oDb->query('SELECT Q_NAME, Q_QUANTITY, Q_PRICE FROM QUERY WHERE Q_NAME=?', 'Eggs')->fetch();
$this->isEqual($aRow, $aInsertValues[1],
	'The data of the row "Eggs" is wrong.');

// Test foreach

$oResults = $oDb->query('SELECT * FROM QUERY');
foreach ($oResults as $aRow)
{
	$this->isFalse(empty($aRow), 'The returned row should not be empty.');
	$this->isTrue(ctype_digit($aRow['Q_ID']), 'The field Q_ID should not be empty.');
}

class weeOracleResult_testForeach extends weeDatabaseRow {}

$oResults = $oDb->query('SELECT * FROM QUERY')->rowClass('weeOracleResult_testForeach');
foreach ($oResults as $aRow)
{
	$this->isInstanceOf($aRow, 'weeOracleResult_testForeach', 'The returned row should be an instance of weeOracleResult_testForeach.');
	$this->isFalse(empty($aRow), 'The returned row should not be empty.');
	$this->isTrue(ctype_digit($aRow['Q_ID']), 'The field Q_ID should not be empty.');
}

// Test the queryValue method

try {
	$this->isEqual($oDb->queryValue('SELECT COUNT(*) FROM QUERY'), count($aInsertValues),
		'queryValue does not return the value expected.');
} catch (UnexpectedValueException $e) {
	$this->fail('queryValue throws an UnexpectedValueException even though the query is known to return only one row of one column.');
}

try {
	$oDb->queryValue('DELETE FROM QUERY WHERE Q_ID=0');
	$this->fail('queryValue does not throw an InvalidArgumentException when the query is not a SELECT query.');
} catch (InvalidArgumentException $e) {}

try {
	$oDb->queryValue('SELECT Q_NAME FROM QUERY WHERE Q_QUANTITY<5');
	$this->fail('queryValue does not throw an UnexpectedValueException when the query returns two rows.');
} catch (UnexpectedValueException $e) {}

try {
	$oDb->queryValue('SELECT Q_NAME, Q_QUANTITY FROM QUERY WHERE Q_QUANTITY=0');
	$this->fail('queryValue does not throw an UnexpectedValueException when the query returns one row of two columns.');
} catch (UnexpectedValueException $e) {}
