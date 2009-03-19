<?php

try {
	try {
		new weePgSQLDatabase(array(
			'host'		=> 'localhost',
			'user'		=> 'wee',
			'password'	=> 'wee',
			'dbname'	=> 'wee_tests',
			'encoding'	=> 'schrodinger'
		));

		$this->fail(_WT('weePGSQLDatabase should throw an InvalidArgumentException when the given encoding is invalid.'));
	} catch (InvalidArgumentException $e) {}

	try {
		$oDb = new weePgSQLDatabase(array(
			'host'		=> 'localhost',
			'user'		=> 'wee',
			'password'	=> 'wee',
			'dbname'	=> 'wee_tests',
			'encoding'	=> 'SQL_ASCII'
		));

		$this->isEqual('SQL_ASCII', $oDb->queryValue('SHOW client_encoding'),
			_WT('weePgSQLDatabase should set the correct encoding to use for the connection as specified in the parameters on initialisation.'));
	} catch (InvalidArgumentException $e) {
		$this->fail(_WT('weePgSQLDatabase should not throw an InvalidArgumentException when the given encoding is valid.'));
	}
} catch (Exception $e) {
	if ($e instanceof ConfigurationException || $e instanceof DatabaseException)
		$this->skip();
	throw $e;
}
