<?php

try {
	try {
		new weePDODatabase;
		$this->fail(_WT('weePDODatabase should throw an InvalidArgumentException when the `dsn` parameter is missing.'));
	} catch (InvalidArgumentException $e) {
	} catch (DatabaseException $e) {
		$this->fail(_WT('weePDODatabase should not throw a DatabaseException when the `dsn` parameter is missing.'));
	}
} catch (ConfigurationException $e) {
	$this->skip();
}

try {
	new weePDODatabase(array('dsn' => 'baddriver:'));
	$this->fail(_WT('weePDODatabase should throw a ConfigurationException when the requested driver does not exist.'));
} catch (ConfigurationException $e) {
} catch (DatabaseException $e) {
	$this->fail(_WT('weePDODatabase should throw a ConfigurationException instead of a DatabaseException when the requested driver does not exist.'));
}
