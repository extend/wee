<?php

try {
	$oDb = new weeSQLiteDatabase(array(
		'create'	=> true,
		'file'		=> ':memory:'
	));
} catch (ConfigurationException $o) {
	$this->skip();
}
