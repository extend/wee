<?php

try {
	try {
		try {
			new weeSQLiteDatabase;
			$this->fail(_WT('weeSQLiteDatabase should throw an InvalidArgumentException when the `file` parameter is missing.'));
		} catch (InvalidArgumentException $e) {}

		try {
			new weeSQLiteDatabase(array(
				'file' => 'file_which_does_not_exist'
			));

			$this->fail(_WT('weeSQLiteDatabase should throw a FileNotFoundException when the database file does not exist and the `create` parameter is missing.'));
		} catch (FileNotFoundException $e) {}
	} catch (DatabaseException $e) {
		$this->fail(_WT('weeSQLiteDatabase should not throw a DatabaseException when one of its parameter is invalid.'));
	}

	try {
		new weeSQLiteDatabase(array(
			'create'	=> true,
			'file'		=> ':memory:'
		));
	} catch (InvalidArgumentException $e) {
		$this->fail(_WT('weeSQLiteDatabase should not throw an InvalidArgumentException when the database file does not exist but the `create` parameter is true.'));
	}
} catch (ConfigurationException $o) {
	$this->skip();
}
