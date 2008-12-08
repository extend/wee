<?php

try {
	$oDb = new weePDODatabase(array(
		'dsn' => 'sqlite2::memory:'
	));
} catch (ConfigurationException $o) {
	$this->skip();
}
