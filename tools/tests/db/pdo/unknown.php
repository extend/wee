<?php

class weePDODatabase_test extends weePDODatabase {
	public function __construct() {}

	public function getDriverName() {
		return 'unknown';
	}
}

$o = new weePDODatabase_test;

try {
	$o->escapeIdent(null);
	$this->fail(_WT('weePDODatabase::escapeIdent should throw a ConfigurationException when the underlying PDO driver is not handled.'));
} catch (ConfigurationException $e) {}
