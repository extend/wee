<?php

class weeDbMetaTable_test extends weeDbMetaTable {
	public function __construct() {}

	public function instantiateObject($sClass, array $aData) {
		return parent::instantiateObject($sClass, $aData);
	}

	public function column($sName) {}
	public function columnExists($sName) {}
	public function getColumnClass() {}
	public function getPrimaryKeyClass() {}
	public function hasPrimaryKey() {}
	public function primaryKey() {}
	protected function queryColumns() {}
}

$o = new weeDbMetaTable_test;

try {
	$o->instantiateObject('stdClass', array());
	$this->fail(_WT('weeDbMetaTable::instantiateObject should throw an InvalidArgumentException when the given class is not a subclass of weeDbMetaTableObject.'));
} catch (InvalidArgumentException $e) {}
