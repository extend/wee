<?php

if (!class_exists('myDbScaffoldModelComplexPKey')) {
	class myDbScaffoldModelComplexPKey extends weeDbModelScaffold
	{
		protected $sSet = 'myDbScaffoldSetComplexPKey';
	}

	class myDbScaffoldSetComplexPKey extends weeDbSetScaffold
	{
		protected $sModel = 'myDbScaffoldModelComplexPKey';
		protected $sTableName = 'dbscaffoldcomplexkey';

		// Making a few things public for testing

		public function buildOrderBy()
		{
			return parent::buildOrderBy();
		}

		public function buildWhere()
		{
			return parent::buildWhere();
		}
	}
}

$oDb->query('BEGIN');

try {
	$oDb->query('CREATE TABLE dbscaffoldcomplexkey (pkey integer, pkeyvarchar varchar(50), other integer, PRIMARY KEY (pkey, pkeyvarchar))');

	$oSet = new myDbScaffoldSetComplexPKey;
	$oSet->setDb($oDb);

	// weeDbSetScaffold::count, weeDbSetScaffold::delete, weeDbSetScaffold::insert

	for ($i = 1; $i <= 42; $i++)
		$oSet->insert(array('pkey' => $i, 'pkeyvarchar' => str_pad('x', $i)));

	$this->isEqual(42, $oSet->count(),
		_WT('weeDbSetScaffold::count does not return the correct number of rows.'));

	// This one does not delete anything
	$oSet->delete(array('pkey' => 33, 'pkeyvarchar' => str_pad('x', 27)));

	$this->isEqual(42, $oSet->count(),
		_WT('weeDbSetScaffold::count does not return the correct number of rows.'));

	$oSet->delete(array('pkey' => 18, 'pkeyvarchar' => str_pad('x', 18)));

	$this->isEqual(41, $oSet->count(),
		_WT('weeDbSetScaffold::count does not return the correct number of rows.'));

	try {
		$oSet->delete(array());
		$this->fail(_WT('weeDbSetScaffold::delete should throw an InvalidArgumentException when the primary key is empty.'));
	} catch (InvalidArgumentException $e) {
	}

	try {
		$oSet->delete(array('invalid' => 22));
		$this->fail(_WT('weeDbSetScaffold::delete should throw an InvalidArgumentException when the primary key is invalid.'));
	} catch (InvalidArgumentException $e) {
	}

	try {
		$oSet->delete(34);
		$this->fail(_WT('The weeDbSetScaffold::delete method should fail when the primary key is incomplete.'));
	} catch (InvalidArgumentException $e) {
	}

	// weeDbSetScaffold::fetch

	$oModel = $oSet->fetch(array('pkey' => 42, 'pkeyvarchar' => str_pad('x', 42)));
	$this->isEqual(42, $oModel['pkey'], _WT('weeDbSetScaffold::fetch returned the wrong answer to the forgotten question.'));

	try {
		$oSet->fetch(array('pkey' => 18, 'pkeyvarchar' => str_pad('x', 18)));
		$this->fail(_WT('weeDbSetScaffold::fetch should have failed when trying to fetch a deleted row.'));
	} catch (DatabaseException $e) {
	}

	try {
		$oSet->fetch(array('pkey' => 18, 'pkeyvarchar' => str_pad('x', 42)));
		$this->fail(_WT('weeDbSetScaffold::fetch should have failed when trying to fetch a non-existent row.'));
	} catch (DatabaseException $e) {
	}

	try {
		$oSet->fetch(array());
		$this->fail(_WT('weeDbSetScaffold::fetch should throw an InvalidArgumentException when the primary key is empty.'));
	} catch (InvalidArgumentException $e) {
	}

	try {
		$oSet->fetch(array('invalid' => 22));
		$this->fail(_WT('weeDbSetScaffold::fetch should throw an InvalidArgumentException when the primary key is invalid.'));
	} catch (InvalidArgumentException $e) {
	}

	// weeDbSetScaffold::fetchAll

	$oResults = $oSet->fetchAll();
	$this->isEqual($oSet->count(), count($oResults),
		_WT('The number of results returned by weeDbSetScaffold::fetchAll is incorrect.'));

	// weeDbSetScaffold::fetchSubset

	try {
		$oResults = $oSet->fetchSubset(5);
		$this->fail(_WT('weeDbSetScaffold::fetchSubset should throw an InvalidArgumentException when only 1 argument is given.'));
	} catch (InvalidArgumentException $e) {
	}

	$oResults = $oSet->fetchSubset(0, 10);
	$this->isEqual(10, count($oResults),
		_WT('The number of results returned by weeDbSetScaffold::fetchSubset is incorrect.'));

	$oResults = $oSet->fetchSubset(5, 10);
	$this->isEqual(10, count($oResults),
		_WT('The number of results returned by weeDbSetScaffold::fetchSubset is incorrect.'));

	// weeDbSetScaffold::orderBy

	$oSet->orderBy(array());
	$this->isEqual('', $oSet->buildOrderBy(), _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	$oSet->orderBy(array('pkey' => 'ASC'));
	$this->isEqual('[pkey] ASC', $oSet->buildOrderBy(), _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	$oSet->orderBy(array('pkey' => 'DESC'));
	$this->isEqual('[pkey] DESC', $oSet->buildOrderBy(), _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	$oSet->orderBy(array('pkey' => 'DESC', 'other'));
	$this->isEqual('[pkey] DESC, [other]', $oSet->buildOrderBy(), _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	$oSet->orderBy(array('pkey' => 'DESC', 'other' => 'DESC'));
	$this->isEqual('[pkey] DESC, [other] DESC', $oSet->buildOrderBy(), _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	try {
		$oSet->orderBy(array('pkey' => 'BAD'));
		$oSet->buildOrderBy();
		$this->fail(_WT('The weeDbSetScaffold::buildOrderBy method should fail when the modifier is unknown.'));
	} catch (InvalidArgumentException $e) {
	}

	// weeDbSetScaffold's subsets

	$oSubset = new myDbScaffoldSetComplexPKey(array('pkey' => array('<=', 10)));
	$oSubset->setDb($oDb);
	$this->isEqual(10, count($oSubset), _WT('The subset count method returned a wrong number of results.'));

	try {
		$oResults = $oSubset->fetchSubset(3);
		$this->fail(_WT('weeDbSetScaffold::fetchSubset should throw an InvalidArgumentException when only 1 arguments are given.'));
	} catch (InvalidArgumentException $e) {
	}

	$oResults = $oSubset->fetchSubset(3, 5);
	$this->isEqual(5, count($oResults), _WT('The weeDbSetScaffold::fetchSubset method returned a wrong number of results.'));

	// weeDbSetScaffold::buildWhere

	$oSubset = new myDbScaffoldSetComplexPKey();
	$oSubset->setDb($oDb);
	$this->isEqual('', $oSubset->buildWhere(), _WT('The weeDbSetScaffold::buildWhere method should have returned an empty WHERE clause.'));

	$oSubset = new myDbScaffoldSetComplexPKey(array('pkey' => 'IS NOT NULL'));
	$oSubset->setDb($oDb);
	$this->isEqual(' WHERE [pkey] IS NOT NULL', $oSubset->buildWhere(), _WT('The weeDbSetScaffold::buildWhere method returned an incorrect WHERE clause.'));

	$oSubset = new myDbScaffoldSetComplexPKey(array('pkey' => array('!=', 17)));
	$oSubset->setDb($oDb);
	$this->isEqual(' WHERE [pkey] != \'17\'', $oSubset->buildWhere(), _WT('The weeDbSetScaffold::buildWhere method returned an incorrect WHERE clause.'));

	$oSubset = new myDbScaffoldSetComplexPKey(array('pkey' => array('IN', 1, 3, 5, 7, 9)));
	$oSubset->setDb($oDb);
	$this->isEqual(' WHERE [pkey] IN (\'1\',\'3\',\'5\',\'7\',\'9\')', $oSubset->buildWhere(), _WT('The weeDbSetScaffold::buildWhere method returned an incorrect WHERE clause.'));

	$oSubset = new myDbScaffoldSetComplexPKey(array('other' => array('LIKE', '%test%')));
	$oSubset->setDb($oDb);
	$this->isEqual(' WHERE [other] LIKE \'%test%\'', $oSubset->buildWhere(), _WT('The weeDbSetScaffold::buildWhere method returned an incorrect WHERE clause.'));

	$oSubset = new myDbScaffoldSetComplexPKey(array('pkey' => array('NOT IN', 1, 3, 5, 7, 9), 'other' => array('LIKE', '%test%')));
	$oSubset->setDb($oDb);
	$this->isEqual(' WHERE [pkey] NOT IN (\'1\',\'3\',\'5\',\'7\',\'9\') AND [other] LIKE \'%test%\'', $oSubset->buildWhere(), _WT('The weeDbSetScaffold::buildWhere method returned an incorrect WHERE clause.'));

	// subset operations

	$oSubset = new myDbScaffoldSetComplexPKey(array('pkey' => array('<=', 10)));
	$oSubset->setDb($oDb);
	$oSubset = $oSubset->subsetIntersect(array('pkey' => array('>=', 5)));
	$this->isEqual(6, count($oSubset), _WT('The subset count method returned a wrong number of results.'));

	// weeDbModelScaffold::update

	$oModel = $oSet->fetch(array('pkey' => 10, 'pkeyvarchar' => str_pad('x', 10)));
	$oModel['other'] = 462;
	$oModel['not_a_column'] = 'And testing it ignoring values not part of the table.';
	$oModel->setDb($oDb)->update();

	$oModel = $oSet->fetch(array('pkey' => 10, 'pkeyvarchar' => str_pad('x', 10)));
	$this->isEqual(462, $oModel['other'], _WT('The weeDbModelScaffold::update method did not save the data correctly.'));

	try {
		$oModel['other'] = 'This is not failing in SQLite because it automatically cast to integer.';
		$oModel->setDb($oDb)->update();
	} catch (DatabaseException $e) {
		$this->fail(_WT('The weeDbModelScaffold::update method should not throw a DatabaseException in SQLite when data type is incorrect.'));
	}

	$oModel = new myDbScaffoldModelComplexPKey(array());
	try {
		$oModel->setDb($oDb)->update();
		$this->fail(_WT('The weeDbModelScaffold::update method should throw an IllegalStateException when no data is given.'));
	} catch (IllegalStateException $e) {
	}

} catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
