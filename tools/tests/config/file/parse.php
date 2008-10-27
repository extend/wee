<?php

class weeFileConfig_parse extends weeFileConfig
{
	// Expose the array
	public $aConfig;

	// Disable the real constructor.
	public function __construct() {}

	// We are testing this method, so let's expose it.
	public function parseLine($sLine)
	{
		parent::parseLine($sLine);
	}
}

$o = new weeFileConfig_parse;

try
{
	$o->parseLine('setting_missing_value');
	$this->fail('weeFileConfig fails to throw an UnexpectedValueException when no equal sign is found');
}
catch (UnexpectedValueException $e) {}

try
{
	$o->parseLine('include');
	$this->fail('weeFileConfig fails to throw an UnexpectedValueException when the include instruction is missing its parameter.');
}
catch (UnexpectedValueException $e) {}

try
{
	$o->parseLine('# a comment');

	$o->parseLine('foo = bar');
	$this->isEqual('bar', $o->aConfig['foo'],
		'weeFileConfig fails to understand a setting assignment.');

	try
	{
		$o->parseLine('include = neither_a_file_nor_an_inclusion');
		$this->isEqual('neither_a_file_nor_an_inclusion', $o->aConfig['include'],
			'weeFileConfig fails to set the value of the "include" setting.');
	}
	catch (FileNotFoundException $e)
	{
		$this->fail('weeFileConfig thinks the "include" setting assignement is an include instruction.');
	}
}
catch (UnexpectedValueException $e)
{
	$this->fail('weeFileConfig fails to properly parse a valid configuration line.');
}
