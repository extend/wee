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
