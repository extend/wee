<?php

try {
	$oDb = new weePDODatabase(array(
		'dsn' => 'sqlite2::memory:'
	));
} catch (Exception $o) {
    if ($o instanceof ConfigurationException || $o instanceof DatabaseException)
        $this->skip();
    throw $e;
}
