<?php

require(ROOT_PATH . 'tools/tests/db/pgsql/connect.php.inc');

class testSet_weeDbSet extends weeDbSet {
	protected $sModel = 'testSet_weeDbModel';

	public function query($sQueryString) {
		return parent::query($sQueryString);
	}

	public function queryRow($sQueryString) {
		return parent::queryRow($sQueryString);
	}

	public function queryValue($sQueryString) {
		return parent::queryValue($sQueryString);
	}
}

class testSet_weeDbModel extends weeDbModel {}

$oSet = new testSet_weeDbSet;

// weeDbSet::getDb

try {
	$oSet->getDb();
	$this->fail(_WT('weeDbSet::getDb should throw an IllegalStateException when trying to get the database associated to this model.'));
} catch (IllegalStateException $e) {}

$oSet->setDb($oDb);

try {
	$oSet->getDb();
} catch (IllegalStateException $e) {
	$this->fail(_WT('weeDbSet::getDb should not throw an IllegalStateException when trying to get the database associated to this model.'));
}

$this->isEqual($oSet->getDb(), $oDb, _WT('weeDbSet::getDb should return the database object we set using weeDbSet::setDb.'));


$oDb->query('CREATE TEMPORARY TABLE dbset (answer integer)');
$oDb->query('INSERT INTO dbset VALUES (42)');

// weeDbSet::queryValue

$this->isEqual($oSet->queryValue('SELECT * FROM dbset'), $oSet->getDb()->queryValue('SELECT * FROM dbset'),
	_WT('weeDbSet::queryValue should return the same result as weeDatabase::queryValue'));

// weeDbSet::query

$m = $oSet->query('SELECT * FROM dbset');
$this->isInstanceOf($m, 'weeDatabaseResult',
	_WT('weeDbSet::query should return a weeDatabaseResult when the request is a not a query returning a result.'));

$this->isInstanceOf($m->fetch(), 'testSet_weeDbModel',
	_WT('weeDatabaseResult instances returned by weeDbSet::query should iterates through instances of weeDbSet::sModel.'));

$this->isNull($oSet->query('DELETE FROM dbset'),
	_WT('weeDbSet::query should not return a value when the request is not a query returning a result.'));

// weeDbSet::queryRow

try {
	$oSet->queryRow('DELETE FROM dbset');
	$this->fail(_WT('weeDbSet::queryRow should throw an UnexpectedValueException when the SQL query did not return a result set.'));
} catch (UnexpectedValueException $e) {}

try {
	$oSet->queryRow('SELECT * FROM dbset');
	$this->fail(_WT('weeDbSet::queryRow should throw a DatabaseException when the result set is empty.'));
} catch (DatabaseException $e) {}

$oDb->query('INSERT INTO dbset VALUES (42)');

$this->isInstanceOf($oSet->queryRow('SELECT * FROM dbset'), 'testSet_weeDbModel',
	_WT('weeDbSet::queryRow should return an instance of weeDbSet::sModel.'));

$oDb->query('INSERT INTO dbset VALUES (42)');

try {
	$oSet->queryRow('SELECT * FROM dbset');
	$this->fail(_WT('weeDbSet::queryRow should throw an DatabaseException when the result set contains more than one row.'));
} catch (DatabaseException $e) {}
