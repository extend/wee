<?php

class weeApplication_cnf extends weeApplication
{
	// We need to expose the app configuration.
	public $aConfig = array(
		'test.string'	=> 'this is a configuration value',
		'test.empty'	=> '',
		'test.zero'		=> '0',
	);

	// We need a public constructor.
	public function __construct() {}
}

$o = new weeApplication_cnf;

$this->isEqual($o->aConfig['test.string'], $o->cnf('test.string'),
	'The value returned for "test.string" did not match the actual configuration value.');

$this->isNotNull($o->cnf('test.empty'),
	'The value returned for "test.empty" should not be null.');
$this->isEqual($o->aConfig['test.empty'], $o->cnf('test.empty'),
	'The value returned for "test.empty" did not match the actual configuration value.');

$this->isNotNull($o->cnf('test.zero'),
	'The value returned for "test.zero" should not be null.');
$this->isEqual($o->aConfig['test.zero'], $o->cnf('test.zero'),
	'The value returned for "test.zero" did not match the actual configuration value.');

$this->isNull($o->cnf('test.undefined'),
	'The value returned for "test.undefined", an undefined configuration value, should be null.');
