<?php

class myDbScaffoldModel extends weeDbModelScaffold
{
	protected $sSet = 'myDbScaffoldSet';
}

class myDbScaffoldSet extends weeDbSetScaffold
{
	protected $sModel = 'myDbScaffoldModel';
	protected $sTableName = 'dbscaffold';

	// Making a few things public for testing

	public $sOrderBy;

	public function searchBuildWhere($aCriteria)
	{
		return parent::searchBuildWhere($aCriteria);
	}
}

require(ROOT_PATH . 'tools/tests/db/pgsql/connect.php.inc');

$oDb->query('BEGIN');

try {
	$oDb->query('CREATE TABLE dbscaffold (pkey integer, other integer, PRIMARY KEY (pkey))');

	$oSet = new myDbScaffoldSet;
	$oSet->setDb($oDb);

	// weeDbSetScaffold::count, weeDbSetScaffold::delete, weeDbSetScaffold::insert

	for ($i = 1; $i <= 42; $i++)
		$oSet->insert(array('pkey' => $i));

	$this->isEqual(42, $oSet->count(),
		_WT('weeDbSetScaffold::count do not return the correct number of rows.'));

	$oSet->delete(33);

	$this->isEqual(41, $oSet->count(),
		_WT('weeDbSetScaffold::count do not return the correct number of rows.'));

	$oSet->delete(array('pkey' => 18));

	$this->isEqual(40, $oSet->count(),
		_WT('weeDbSetScaffold::count do not return the correct number of rows.'));

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

	// weeDbSetScaffold::fetch

	$oModel = $oSet->fetch(42);
	$this->isEqual(42, $oModel['pkey'], _WT('weeDbSetScaffold::fetch returned the wrong answer to the forgotten question.'));

	try {
		$oSet->fetch(33);
		$this->fail(_WT('weeDbSetScaffold::fetch should have failed when trying to fetch a deleted row.'));
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

	$oResults = $oSet->fetchSubset(5);
	$this->isEqual($oSet->count() - 5, count($oResults),
		_WT('The number of results returned by weeDbSetScaffold::fetchSubset is incorrect.'));

	$oResults = $oSet->fetchSubset(0, 10);
	$this->isEqual(10, count($oResults),
		_WT('The number of results returned by weeDbSetScaffold::fetchSubset is incorrect.'));

	$oResults = $oSet->fetchSubset(5, 10);
	$this->isEqual(10, count($oResults),
		_WT('The number of results returned by weeDbSetScaffold::fetchSubset is incorrect.'));

	// weeDbSetScaffold::orderBy

	$oSet->orderBy(array());
	$this->isEqual('', $oSet->sOrderBy, _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	$oSet->orderBy(array('pkey' => 'ASC'));
	$this->isEqual('"pkey" ASC', $oSet->sOrderBy, _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	$oSet->orderBy(array('pkey' => 'DESC'));
	$this->isEqual('"pkey" DESC', $oSet->sOrderBy, _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	$oSet->orderBy(array('pkey' => 'DESC', 'other'));
	$this->isEqual('"pkey" DESC, "other"', $oSet->sOrderBy, _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	$oSet->orderBy(array('pkey' => 'DESC', 'other' => 'DESC'));
	$this->isEqual('"pkey" DESC, "other" DESC', $oSet->sOrderBy, _WT('The weeDbSetScaffold::orderBy method built a bad ORDER BY expression.'));

	try {
		$oSet->orderBy(array('pkey' => 'BAD'));
		$this->fail(_WT('The weeDbSetScaffold::orderBy method should fail when the modifier is unknown.'));
	} catch (InvalidArgumentException $e) {
	}

	// weeDbSetScaffold::search

	$oResults = $oSet->search(array('pkey' => array('<=', 10)));
	$this->isEqual(10, count($oResults), _WT('The weeDbSetScaffold::search method returned a wrong number of results.'));

	$oResults = $oSet->search(array('pkey' => array('<=', 10)), 3);
	$this->isEqual(7, count($oResults), _WT('The weeDbSetScaffold::search method returned a wrong number of results.'));

	$oResults = $oSet->search(array('pkey' => array('<=', 10)), 3 , 5);
	$this->isEqual(5, count($oResults), _WT('The weeDbSetScaffold::search method returned a wrong number of results.'));

	// weeDbSetScaffold::searchBuildWhere

	$sWhere = $oSet->searchBuildWhere(array());
	$this->isEqual('TRUE', $sWhere, _WT('The weeDbSetScaffold::searchBuildWhere method returned an incorrect WHERE clause.'));

	$sWhere = $oSet->searchBuildWhere(array('pkey' => 'IS NOT NULL'));
	$this->isEqual('TRUE AND "pkey" IS NOT NULL', $sWhere, _WT('The weeDbSetScaffold::searchBuildWhere method returned an incorrect WHERE clause.'));

	$sWhere = $oSet->searchBuildWhere(array('pkey' => array('!=', 17)));
	$this->isEqual('TRUE AND "pkey" != \'17\'', $sWhere, _WT('The weeDbSetScaffold::searchBuildWhere method returned an incorrect WHERE clause.'));

	$sWhere = $oSet->searchBuildWhere(array('pkey' => array('IN', 1, 3, 5, 7, 9)));
	$this->isEqual('TRUE AND "pkey" IN (\'1\',\'3\',\'5\',\'7\',\'9\')', $sWhere, _WT('The weeDbSetScaffold::searchBuildWhere method returned an incorrect WHERE clause.'));

	$sWhere = $oSet->searchBuildWhere(array('other' => array('LIKE', '%test%')));
	$this->isEqual('TRUE AND "other" LIKE \'%test%\'', $sWhere, _WT('The weeDbSetScaffold::searchBuildWhere method returned an incorrect WHERE clause.'));

	$sWhere = $oSet->searchBuildWhere(array('pkey' => array('NOT IN', 1, 3, 5, 7, 9), 'other' => array('LIKE', '%test%')));
	$this->isEqual('TRUE AND "pkey" NOT IN (\'1\',\'3\',\'5\',\'7\',\'9\') AND "other" LIKE \'%test%\'', $sWhere, _WT('The weeDbSetScaffold::searchBuildWhere method returned an incorrect WHERE clause.'));

	// weeDbSetScaffold::searchCount

	$this->isEqual(10, $oSet->searchCount(array('pkey' => array('<=', 10))), _WT('The weeDbSetScaffold::searchCount method returned a wrong number of results.'));

	// weeDbModelScaffold::update

	$oModel = $oSet->fetch(10);
	$oModel['other'] = 462;
	$oModel['not_a_column'] = 'And testing it ignoring values not part of the table.';
	$oModel->setDb($oDb)->update();

	$oModel = $oSet->fetch(10);
	$this->isEqual(462, $oModel['other'], _WT('The weeDbModelScaffold::update method did not save the data correctly.'));

	try {
		$oModel['other'] = 'This is going to fail hard.';
		$oModel->setDb($oDb)->update();
		$this->fail(_WT('The weeDbModelScaffold::update method should throw a DatabaseException when data type is incorrect.'));
	} catch (DatabaseException $e) {
	}

	$oModel = new myDbScaffoldModel(array());
	try {
		$oModel->setDb($oDb)->update();
		$this->fail(_WT('The weeDbModelScaffold::update method should throw an IllegalStateException when no data is given.'));
	} catch (IllegalStateException $e) {
	}

} catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
