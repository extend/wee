<?php

try {
	try {
		new weeMySQLDatabase(array(
			'host'		=> 'localhost',
			'user'		=> 'wee',
			'password'	=> 'wee',
			'dbname'	=> 'wee_tests',
			'encoding'	=> 'schrodinger'
		));

		$this->fail(_WT('weeMySQLDatabase should throw an InvalidArgumentException when the given encoding is invalid.'));
	} catch (InvalidArgumentException $e) {}

	try {
		$oDb = new weeMySQLDatabase(array(
			'host'		=> 'localhost',
			'user'		=> 'wee',
			'password'	=> 'wee',
			'dbname'	=> 'wee_tests',
			'encoding'	=> 'macroman'
		));

		$this->isEqual('macroman', $oDb->queryValue('SELECT @@character_set_client'),
			_WT('weeMySQLDatabase should set the correct encoding to use for the connection as specified in the parameters on initialisation.'));
	} catch (InvalidArgumentException $e) {
		$this->fail(_WT('weeMySQLDatabase should not throw an InvalidArgumentException when the given encoding is valid.'));
	}
} catch (Exception $e) {
	if ($e instanceof ConfigurationException || $e instanceof DatabaseException)
		$this->skip();
	throw $e;
}
