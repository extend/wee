<?php

try
{
	$o = new weeFileConfig(dirname(__FILE__) . '/file_which_does_not_exist.cnf');
	$this->fail('weeFileConfig does not throw a FileNotFoundException when the specified file does not exist.');
}
catch (FileNotFoundException $e) {}

$o = new weeFileConfig(dirname(__FILE__) . '/simple.cnf');

$this->isTrue(isset($o['test']),
	'The configuration object does not have a "test" entry even though it has been defined in the sample configuration.');

$this->isFalse(isset($o['blah']),
	'The configuration object have a "blah" entry even though it has been commented in the sample configuration file.');

$this->isEqual('bar', $o['foo'],
	'The value of the "foo" entry in the configuration object is not the one defined in the sample configuration file.');

$this->isNull($o['entry_that_does_not_exist'],
	'The value of an undefined entry in the configuration object is not null.');

