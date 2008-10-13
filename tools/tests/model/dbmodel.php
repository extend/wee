<?php

require(ROOT_PATH . 'tools/tests/db/pgsql/connect.php.inc');

class test_weeDbSet extends weeDbSet {
	protected $sModel = 'test_weeDbModel';
	public function query($sQueryString) {
		return parent::query($sQueryString);
	}
}
class test_weeDbModel extends weeDbModel {}

$oModel = new test_weeDbModel;

// weeDbModel::getDb

try {
	$oModel->getDb();
	$this->fail(_('weeDbModel::getDb should throw an IllegalStateException when trying to get the database associated to this model.'));
} catch (IllegalStateException $e) {}

$oModel->setDb($oDb);

try {
	$oModel->getDb();
} catch (IllegalStateException $e) {
	$this->fail(_('weeDbModel::getDb should not throw an IllegalStateException when trying to get the database associated to this model.'));
}

$this->isEqual($oModel->getDb(), $oDb, _('weeDbModel::getDb should return the database object we set using weeDbModel::setDb.'));

$oSet = new test_weeDbSet;

// weeDbSet::getDb

try {
	$oSet->getDb();
	$this->fail(_('weeDbSet::getDb should throw an IllegalStateException when trying to get the database associated to this model.'));
} catch (IllegalStateException $e) {}

$oSet->setDb($oDb);

try {
	$oSet->getDb();
} catch (IllegalStateException $e) {
	$this->fail(_('weeDbSet::getDb should not throw an IllegalStateException when trying to get the database associated to this model.'));
}

$this->isEqual($oSet->getDb(), $oDb, _('weeDbSet::getDb should return the database object we set using weeDbSet::setDb.'));

$oDb->query('BEGIN');

try {
	$oDb->query('CREATE TABLE dbmodel (answer integer)');
	$oDb->query("INSERT INTO dbmodel VALUES (42)");

	// weeDbSet::query

	$m = $oSet->query('SELECT * FROM dbmodel');
	$this->isInstanceOf($m, 'weeDatabaseResult',
		_('weeDbSet::query should return a weeDatabaseResult when the request is a not a query returning a result.'));

	$this->isInstanceOf($m->fetch(), 'test_weeDbModel',
		_('weeDatabaseResult instances returned by weeDbSet::query should iterates through instances of weeDbSet::sModel.'));

	$this->isNull($oSet->query('DELETE FROM dbmodel'),
		_('weeDbSet::query should not return a value when the request is not a query returning a result.'));
} catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
