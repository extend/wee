<?php

try {
	$oDb = new weePDODatabase(array(
		'dsn' => 'sqlite::memory:'
	));
} catch (ConfigurationException $o) {
    if ($o instanceof ConfigurationException || $o instanceof DatabaseException)
        $this->skip();
    throw $e;
}
