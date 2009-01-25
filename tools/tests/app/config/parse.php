<?php

class weeConfigFile_parse extends weeConfigFile
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

$o = new weeConfigFile_parse;

try
{
	$o->parseLine('setting_missing_value');
	$this->fail(_WT('weeConfigFile fails to throw an UnexpectedValueException when no equal sign is found'));
}
catch (UnexpectedValueException $e) {}

try
{
	$o->parseLine('include');
	$this->fail(_WT('weeConfigFile fails to throw an UnexpectedValueException when the include instruction is missing its parameter.'));
}
catch (UnexpectedValueException $e) {}

try
{
	$o->parseLine('# a comment');

	$o->parseLine('foo = bar');
	$this->isEqual('bar', $o->aConfig['foo'],
		_WT('weeConfigFile fails to understand a setting assignment.'));

	try
	{
		$o->parseLine('include = neither_a_file_nor_an_inclusion');
		$this->isEqual('neither_a_file_nor_an_inclusion', $o->aConfig['include'],
			_WT('weeConfigFile fails to set the value of the "include" setting.'));
	}
	catch (FileNotFoundException $e)
	{
		$this->fail(_WT('weeConfigFile thinks the "include" setting assignement is an include instruction.'));
	}
}
catch (UnexpectedValueException $e)
{
	$this->fail(_WT('weeConfigFile fails to properly parse a valid configuration line.'));
}
