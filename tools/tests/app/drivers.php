<?php

class weeApplication_test_driver
{
	public function __construct($aParams)
	{
	}
}

class weeApplication_drivers extends weeApplication
{
	// We need to expose the app configuration.
	public $aConfig = array(
		'test.driver' => 'weeApplication_test_driver',
	);

	// And the drivers array
	public $aDrivers = array();

	// We need a public constructor.
	public function __construct() {}
}

$o = new weeApplication_drivers;

$this->isTrue(empty($o->aDrivers),
	'weeApplication::$aDrivers should be empty.');

$oTestDriver = $o->test;
$this->isFalse(empty($o->aDrivers),
	'weeApplication::$aDrivers should not be empty.');
$this->isEqual($o->aDrivers['test'], $oTestDriver,
	'The driver we got is different than the one we asked for.');

try {
	$o->unknownDriver;
	$this->fail('An InvalidArgumentException should have been thrown when trying to get an unknown driver.');
} catch (InvalidArgumentException $e) {
}
