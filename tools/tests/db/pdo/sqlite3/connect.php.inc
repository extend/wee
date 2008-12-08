<?php

try {
	$oDb = new weePDODatabase(array(
		'dsn' => 'sqlite::memory:'
	));
} catch (ConfigurationException $o) {
	$this->skip();
}
