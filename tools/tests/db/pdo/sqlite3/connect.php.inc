<?php

try {
	$oDb = new weePDODatabase(array(
		'dsn' => 'sqlite::memory:'
	));
} catch (Exception $o) {
    if ($o instanceof ConfigurationException || $o instanceof DatabaseException)
        $this->skip();
    throw $e;
}
