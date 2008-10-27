<?php

class weeFileConfig_basic extends weeFileConfig
{
	// Expose the array
	public $aConfig;
}

try
{
	$o = new weeFileConfig_basic(dirname(__FILE__) . '/file_which_does_not_exist.cnf');
	$this->fail('weeFileConfig does not throw a FileNotFoundException when the specified file does not exist.');
}
catch (FileNotFoundException $e) {}

$o = new weeFileConfig_basic(dirname(__FILE__) . '/basic.cnf');

$this->isTrue(isset($o->aConfig['test']),
	'The configuration object does not have a "test" entry even though it has been defined in the sample configuration.');

$this->isFalse(isset($o->aConfig['blah']),
	'The configuration object have a "blah" entry even though it has been commented in the sample configuration file.');

$this->isEqual('bar', $o->aConfig['foo'],
	'The value of the "foo" entry in the configuration object is not the one defined in the sample configuration file.');

$this->isFalse(array_key_exists('entry_that_does_not_exist', $o->aConfig),
	'The value of an undefined entry in the configuration object was found to exist...');
