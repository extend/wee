<?php

class weeApplication_cnfarray extends weeApplication
{
	// We need to expose the app configuration.
	public $aConfig = array(
		'test.string'	=> 'this is a configuration value',
		'test.empty'	=> '',
		'test.zero'		=> '0',
		'test2.dummy'	=> 'udonge',
	);

	// We need a public constructor.
	public function __construct() {}
}

$o = new weeApplication_cnfarray;

$aEmptyConfig = $o->cnfArray('empty');
$this->isTrue(empty($aEmptyConfig),
	_WT('The "empty" option group should not have been found.'));

$aTestConfig = $o->cnfArray('test');
$this->isEqual(3, count($aTestConfig),
	_WT('The number of items in "test" is wrong.'));
$this->isEqual($o->aConfig['test.string'], $aTestConfig['string'],
	_WT('The value for "string" is wrong.'));
$this->isEqual($o->aConfig['test.empty'], $aTestConfig['empty'],
	_WT('The value for "empty" is wrong.'));
$this->isEqual($o->aConfig['test.zero'], $aTestConfig['zero'],
	_WT('The value for "zero" is wrong.'));
$this->isTrue(empty($aTestConfig['dummy']),
	_WT('The value for "dummy" should not have been returned.'));
