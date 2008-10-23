<?php

require(ROOT_PATH . 'tools/tests/db/pgsql/connect.php.inc');

class test_weeDbModel extends weeDbModel {
	public function query($sQueryString) {
		return parent::query($sQueryString);
	}
}
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

$this->isEqual($oDb, $oModel->getDb(),
	_('weeDbModel::getDb should return the database object we set using weeDbModel::setDb.'));

$oDb->query('BEGIN');

try {
	$oDb->query('CREATE TABLE dbmodel (answer integer)');
	$oDb->query("INSERT INTO dbmodel VALUES (42)");

	// weeDbModel::query

	$m = $oModel->query('SELECT * FROM dbmodel');
	$this->isInstanceOf($m, 'weeDatabaseResult',
		_('weeDbModel::query should return a weeDatabaseResult when the request is a not a query returning a result.'));

	$this->isTrue(is_array($m->fetch()),
		_('weeDatabaseResult instances returned by weeDbModel::query should not be associated to a row model.'));

	$this->isNull($oModel->query('DELETE FROM dbmodel'),
		_('weeDbModel::query should not return a value when the request is not a query returning a result.'));
} catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
