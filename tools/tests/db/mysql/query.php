<?php

require('connect.php.inc');

// Create the test table

$oDb->query('
	CREATE TEMPORARY TABLE query (
		q_id SERIAL NOT NULL,
		q_name CHARACTER VARYING(50),
		q_quantity INTEGER,
		q_price NUMERIC(12,2)
	);
');

// Insert values into the test table

$aInsertValues = array(
	array(
		'q_name'		=> 'Web:Extend Downloads',
		'q_quantity'	=> 8,
		'q_price'		=> 0,
	),
	array(
		'q_name'		=> 'Eggs',
		'q_quantity'	=> 12,
		'q_price'		=> 6.99,
	),
	array(
		'q_name'		=> 'Shoes',
		'q_quantity'	=> 3,
		'q_price'		=> 25.95,
	),
	array(
		'q_name'		=> 'Songs',
		'q_quantity'	=> 1000000,
		'q_price'		=> 0.99,
	),
	array(
		'q_name'		=> 'Years',
		'q_quantity'	=> 2008,
		'q_price'		=> 42,
	),
	array(
		'q_name'		=> 'Xmas Trees',
		'q_quantity'	=> 1225,
		'q_price'		=> 13.50,
	),
	array(
		'q_name'		=> 'Seven Dwarfs',
		'q_quantity'	=> 7,
		'q_price'		=> 123,
	),
	array(
		'q_name'		=> 'Computers',
		'q_quantity'	=> 6,
		'q_price'		=> 2500,
	),
	array(
		'q_name'		=> 'Girls',
		'q_quantity'	=> 0,
		'q_price'		=> 9999999999,
	),
	array(
		'q_name'		=> 'Items',
		'q_quantity'	=> 10,
		'q_price'		=> 0.50,
	),
);

foreach ($aInsertValues as $aRow)
	$oDb->query('INSERT INTO query (q_name, q_quantity, q_price)
		VALUES (:q_name, :q_quantity, :q_price)', $aRow);

// Do various queries to check our data (and our query method)

$aRow = $oDb->query('SELECT COUNT(*) AS c FROM query')->fetch();
$this->isEqual($aRow['c'], sizeof($aInsertValues),
	"The total number of rows in the table isn't matching the number of rows inserted.");

$aRow = $oDb->query('SELECT COUNT(*) AS c FROM query WHERE q_quantity>=?', 10)->fetch();
$this->isEqual($aRow['c'], 5,
	'The number of rows with q_quantity>=10 is wrong.');

$aRow = $oDb->query('SELECT COUNT(*) AS c FROM query WHERE q_price<?', 13.55)->fetch();
$this->isEqual($aRow['c'], 5,
	'The number of rows with q_price<13.55 is wrong.');

$aRow = $oDb->query('SELECT COUNT(*) AS c FROM query WHERE q_name=? LIMIT 1', 'Cute Girls')->fetch();
$this->isEqual($aRow['c'], 0,
	'There is no cute girl in this table.');

$aRow = $oDb->query('SELECT COUNT(*) AS c FROM query WHERE q_quantity>=? AND q_price<?', 10, 13.55)->fetch();
$this->isEqual($aRow['c'], 4,
	'The number of rows with q_quantity>=10 AND q_price<13.55 is wrong.');

$aRow = $oDb->query('SELECT q_name, q_quantity, q_price FROM query WHERE q_name=? LIMIT 1', 'Eggs')->fetch();
$this->isEqual($aRow, $aInsertValues[1],
	'The data of the row "Eggs" is wrong.');

// Test foreach

$oResults = $oDb->query('SELECT * FROM query');
foreach ($oResults as $aRow)
{
	$this->isFalse(empty($aRow), 'The returned row should not be empty.');
	$this->isTrue(ctype_digit($aRow['q_id']), 'The field q_id should not be empty.');
}

class weeMysqlResult_testForeach extends weeDatabaseRow {}

$oResults = $oDb->query('SELECT * FROM query')->rowClass('weeMysqlResult_testForeach');
foreach ($oResults as $aRow)
{
	$this->isInstanceOf($aRow, 'weeMysqlResult_testForeach', 'The returned row should be an instance of weeMysqlResult_testForeach.');
	$this->isFalse(empty($aRow), 'The returned row should not be empty.');
	$this->isTrue(ctype_digit($aRow['q_id']), 'The field q_id should not be empty.');
}

// Test the queryValue method

try {
	$this->isEqual($oDb->queryValue('SELECT COUNT(*) FROM query'), count($aInsertValues),
		'queryValue does not return the value expected.');
} catch (UnexpectedValueException $e) {
	$this->fail('queryValue throws an UnexpectedValueException even though the query is known to return only one row of one column.');
}

try {
	$oDb->queryValue('DELETE FROM query WHERE false');
	$this->fail('queryValue does not throw an InvalidArgumentException when the query is not a SELECT query.');
} catch (InvalidArgumentException $e) {}

try {
	$oDb->queryValue('SELECT q_name FROM query LIMIT 2');
	$this->fail('queryValue does not throw an UnexpectedValueException when the query returns two rows.');
} catch (UnexpectedValueException $e) {}

try {
	$oDb->queryValue('SELECT q_name, q_quantity FROM query LIMIT 1');
	$this->fail('queryValue does not throw an UnexpectedValueException when the query returns one row of two columns.');
} catch (UnexpectedValueException $e) {}
