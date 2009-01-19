<?php

class weeConfigFile_basic extends weeConfigFile
{
	// Expose the array
	public $aConfig;
}

try
{
	$o = new weeConfigFile_basic(dirname(__FILE__) . '/file_which_does_not_exist.cnf');
	$this->fail(_WT('weeConfigFile does not throw a FileNotFoundException when the specified file does not exist.'));
}
catch (FileNotFoundException $e) {}

$o = new weeConfigFile_basic(dirname(__FILE__) . '/basic.cnf');

$this->isTrue(isset($o->aConfig['test']),
	_WT('The configuration object does not have a "test" entry even though it has been defined in the sample configuration.'));

$this->isFalse(isset($o->aConfig['blah']),
	_WT('The configuration object have a "blah" entry even though it has been commented in the sample configuration file.'));

$this->isEqual('bar', $o->aConfig['foo'],
	_WT('The value of the "foo" entry in the configuration object is not the one defined in the sample configuration file.'));

$this->isFalse(array_key_exists('entry_that_does_not_exist', $o->aConfig),
	_WT('The value of an undefined entry in the configuration object was found to exist...'));
