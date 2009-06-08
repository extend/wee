<?php

try {
	new weePgSQLDatabase(array('host' => 'foo.bar'));
} catch (ErrorException $e) {
	$this->fail(_WT('weePgSQLDatabase::__construct should not throw an ErrorException when the connection failed.'));
} catch (DatabaseException $e) {
} catch (ConfigurationException $e) {
	$this->skip();
}
