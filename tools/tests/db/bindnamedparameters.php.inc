<?php

$this->isFalse(empty($oDb), sprintf(_WT('The file %s requires a database object $oDb in the current scope before it is included.'), __FILE__));

if (!class_exists('weeDatabase_testBindNamedParameters', false)) {
	class weeDatabase_testBindNamedParameters extends weeDatabase {
		protected $oDb;

		public function __construct(weeDatabase $oDb) {
			$this->oDb = $oDb;
		}

		public function bindNamedParameters($sQueryString, $aParameters) {
			return $this->oDb->bindNamedParameters($sQueryString, $aParameters);
		}

		// abstract methods of weeDatabase
		protected function doEscape($sValue) {}
		protected function doQuery($sQuery) {}
		public function escapeIdent($sValue) {}
		public function getLastError() {}
		public function getPKId($sName = null) {}
		public function numAffectedRows($sName = null) {}
		public function prepare($sQuery) {}
	}
}

$oDb = new weeDatabase_testBindNamedParameters($oDb);

// Test the method weeDatabase::bindNamedParameters

try {
	$oDb->bindNamedParameters(
		'SELECT * FROM table WHERE my_string=:my_string AND my_int=:my_int AND my_float=:my_float LIMIT 1',
		array('my_string' => 'eggs are good, yup, yum', 'my_int' => 42, 'my_float' => 2008.2008)
	);
} catch (DatabaseException $e) {
	$this->fail(_WT('weeDatabase::bindNamedParameters should not throw an exception for a random value.'));
}

try {
	$oDb->bindNamedParameters(
		'SELECT * FROM table WHERE my_field=:my_value LIMIT 1',
		array('my_value' => null)
	);
} catch (DatabaseException $e) {
	$this->fail(_WT('weeDatabase::bindNamedParameters should not throw an exception for null values.'));
}
