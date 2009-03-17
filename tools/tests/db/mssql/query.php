<?php

if (!isset($oDb))
	require('connect.php.inc');

// Create the test table

$oDb->query('
	CREATE TABLE #query (
		q_id int IDENTITY PRIMARY KEY,
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

try {
	foreach ($aInsertValues as $aRow)
		$oDb->query('INSERT INTO #query (q_name, q_quantity, q_price)
			VALUES (:q_name, :q_quantity, :q_price)', $aRow);

	// Do various queries to check our data (and our query method)

	$aRow = $oDb->query('SELECT COUNT(*) AS c FROM #query')->fetch();
	$this->isEqual(count($aInsertValues), $aRow['c'],
		_WT("The total number of rows in the table isn't matching the number of rows inserted."));

	$aRow = $oDb->query('SELECT COUNT(*) AS c FROM #query WHERE q_quantity>=?', 10)->fetch();
	$this->isEqual(5, $aRow['c'],
		_WT('The number of rows with q_quantity>=10 is wrong.'));

	$aRow = $oDb->query('SELECT COUNT(*) AS c FROM #query WHERE q_price<?', 13.55)->fetch();
	$this->isEqual(5, $aRow['c'],
		_WT('The number of rows with q_price<13.55 is wrong.'));

	$aRow = $oDb->query('SELECT TOP 1 COUNT(*) AS c FROM #query WHERE q_name=?', 'Cute Girls')->fetch();
	$this->isEqual(0, $aRow['c'],
		_WT('There is no cute girl in this table.'));

	$aRow = $oDb->query('SELECT COUNT(*) AS c FROM #query WHERE q_quantity>=? AND q_price<?', 10, 13.55)->fetch();
	$this->isEqual(4, $aRow['c'],
		_WT('The number of rows with q_quantity>=10 AND q_price<13.55 is wrong.'));

	$aRow = $oDb->query('SELECT TOP 1 q_name, q_quantity, q_price FROM #query WHERE q_name=?', 'Eggs')->fetch();
	$this->isEqual($aInsertValues[1], $aRow,
		_WT('The data of the row "Eggs" is wrong.'));

	// Test fetch

	try {
		$oDb->query('SELECT * FROM #query')->fetch();
		$this->fail(_WT('weeDatabaseResult::fetch must throw a DatabaseException when more than one row is found.'));
	} catch (DatabaseException $e) {}

	$o = $oDb->query('SELECT * FROM #query WHERE q_quantity=462');

	$this->isTrue(is_object($o),
		_WT('weeDatabaseResult::fetch should return an object when the query is a SELECT query, even when its result set is empty.'));

	try {
		$o->fetch();
		$this->fail(_WT('weeDatabaseResult::fetch must throw a DatabaseException when 0 row is found.'));
	} catch (DatabaseException $e) {}

	$oResults = $oDb->query('SELECT * FROM #query');

	// Test Iterator

	$oResults->rewind();
	$this->isTrue($oResults->valid(),
		_WT('weeMySQLResult::valid should return true after a call to weeMySQLResult::rewind when the result set is not empty.'));

	$this->isEqual(array('q_id' => 1) + $aInsertValues[0], $oResults->current(),
		_WT('weeMySQLResult::current should return the first row of the result set after a call to weeMySQLResult::rewind.'));

	$oResults->next();
	$this->isEqual(array('q_id' => 2) + $aInsertValues[1], $oResults->current(),
		_WT('weeMySQLResult::current should return the second row of the result set when moving forward to the second row.'));

	// Test foreach

	foreach ($oResults as $aRow) {
		$this->isFalse(empty($aRow), _WT('The returned row should not be empty.'));
		$this->isTrue(is_int($aRow['q_id']) || ctype_digit($aRow['q_id']), _WT('The field q_id should not be empty.'));
	}

	if (!class_exists('weeMSSQLResult_testForeach')) {
		class weeMSSQLResult_testForeach extends weeDatabaseRow {}
	}

	$oResults = $oDb->query('SELECT * FROM #query')->rowClass('weeMSSQLResult_testForeach');
	foreach ($oResults as $aRow) {
		$this->isInstanceOf($aRow, 'weeMSSQLResult_testForeach', _WT('The returned row should be an instance of weeMSSQLResult_testForeach.'));
		$this->isFalse(empty($aRow), _WT('The returned row should not be empty.'));
		$this->isTrue(is_int($aRow['q_id']) || ctype_digit($aRow['q_id']), _WT('The field q_id should not be empty.'));
	}

	// Test the queryValue method

	try {
		$this->isEqual(count($aInsertValues), $oDb->queryValue('SELECT count(*) FROM #query'),
			_WT('queryValue does not return the value expected.'));
	} catch (UnexpectedValueException $e) {
		$this->fail(_WT('queryValue throws an UnexpectedValueException even though the query is known to return only one row of one column.'));
	}

	try {
		$oDb->queryValue('DELETE FROM #query WHERE 1 = 0');
		$this->fail(_WT('queryValue does not throw an InvalidArgumentException when the query is not a SELECT query.'));
	} catch (InvalidArgumentException $e) {}

	try {
		$oDb->queryValue('SELECT TOP 2 q_name FROM #query');
		$this->fail(_WT('queryValue does not throw an UnexpectedValueException when the query returns two rows.'));
	} catch (UnexpectedValueException $e) {}

	try {
		$oDb->queryValue('SELECT TOP 1 q_name, q_quantity FROM #query');
		$this->fail(_WT('queryValue does not throw an UnexpectedValueException when the query returns one row of two columns.'));
	} catch (UnexpectedValueException $e) {}

	// weeMSSQLResult::fetchAll

	$aAll = array();
	foreach ($aInsertValues as $i => $aRow)
		$aAll[] = array('q_id' => $i + 1) + $aRow;

	$this->isEqual($aAll, $oDb->query('SELECT * FROM #query')->fetchAll(),
		_WT('weeMSSQLResult::fetchAll does not return the expected rows of the table.'));

	$this->isEqual(array(), $oDb->query('SELECT * FROM #query WHERE 1 = 0')->fetchAll(),
		_WT('weeMSSQLResult::fetchAll does not correctly return an empty array when the result set is empty.'));
} catch (Exception $eException) {}

$oDb->query('DROP TABLE #query');
if (isset($eException))
	throw $eException;
