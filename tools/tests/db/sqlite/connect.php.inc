<?php

try {
	$oDb = new weeSQLiteDatabase(array(
		'create'	=> true,
		'file'		=> ROOT_PATH . 'app/tmp/wee_tests.db'
	));
} catch (ConfigurationException $o) {
	$this->skip();
}
