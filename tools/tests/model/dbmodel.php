<?

require(ROOT_PATH . 'tools/tests/db/pgsql/connect.php.inc');

class test_weeDbModel extends weeDbModel {}

try {
	$o = new test_weeDbModel;
	$o->getDb();
	$this->fail(_('weeDbModel::getDb should throw an IllegalStateException when trying to get the database associated to this model.'));
} catch (IllegalStateException $e) {}

try {
	$o = new test_weeDbModel;
	$o->setDb($oDb);
	$oDbModel = $o->getDb();
	$this->isEqual($oDbModel, $oDb, _('weeDbModel::getDb should return the database object we set using weeDbModel::setDb.'));
} catch (IllegalStateException $e) {
	$this->fail(_('weeDbModel::getDb should not throw an IllegalStateException when trying to get the database associated to this model.'));
}
