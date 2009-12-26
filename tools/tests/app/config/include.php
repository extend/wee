<?php

class weeConfigFile_include extends weeConfigFile
{
	// Expose the array
	public $aConfig;

	// Let's use a dummy constructor to test getIncludeFilename() without the 
	// need of a true configuration object.
	public function __construct() {}

	// We need to expose this one to call it at will too.
	public function parseFile($sFilename)
	{
		parent::parseFile($sFilename);
	}

	// We are testing this method so let's expose it.
	public function getIncludeFilename($sPath)
	{
		return parent::getIncludeFilename($sPath);
	}

	// We need to fake the fact that there is already a file in the stack 
	// to test the behaviour of the method when $sPath begins with "./".
	public function getFakeIncludeFilename($sPath)
	{
		$this->aFilesStack[] = 'subdir/fake.cnf';
		$s = $this->getIncludeFilename($sPath);
		array_pop($this->aFilesStack);

		return $s;
	}

	// Overlooaded to check whether conditional inclusion works.
	protected function getTargetFunctions()
	{
		static $aFunc = array(
			'is_foo' => '"foo" == ":1"'
		);

		return $aFunc;
	}
}

$o = new weeConfigFile_include;

$this->isEqual('relative.cnf', $o->getIncludeFilename('relative.cnf'),
	_WT('weeConfigFile::getIncludeFilename() does not understand normal relative paths.'));

$this->isEqual('/absolute.cnf', $o->getIncludeFilename('/absolute.cnf'),
	_WT('weeConfigFile::getIncludeFilename() does not understand normal absolute paths.'));

$this->isEqual('subdir/relative.cnf', $o->getFakeIncludeFilename('./relative.cnf'),
	_WT('weeConfigFile::getIncludeFilename() does not understand paths beginning with "./".'));

$this->isEqual(ROOT_PATH . 'absolute.cnf', $o->getIncludeFilename('//absolute.cnf'),
	_WT('weeConfigFile::getIncludeFilename() does not understand paths beginning with "//".'));

try
{
	$o->parseFile(dirname(__FILE__) . '/bad_include.cnf');
	$this->fail(_WT('weeConfigFile does not throw a FileNotFoundException when a file which does not exist is included.'));
}
catch (FileNotFoundException $e) {}

try
{
	$o->parseFile(dirname(__FILE__) . '/recursive.cnf');
	// If the exception is not thrown, the next line will not be executed because the execution stack will explode.
	$this->fail(_WT('weeConfigFile fails to throw an UnexpectedValueException when there is a loop in the inclusions.'));
}
catch (UnexpectedValueException $e) {}

try
{
	$o->parseFile(dirname(__FILE__) . '/recursive1.cnf');
	// If the exception is not thrown, the next line will not be executed because the execution stack will explode.
	$this->fail(_WT('weeConfigFile fails to throw an UnexpectedValueException when there is a circuit in the inclusions.'));
}
catch (UnexpectedValueException $e) {}

try
{
	$o->parseFile(dirname(__FILE__) . '/include.cnf');
	$this->isEqual('1', $o->aConfig['test'],
		_WT('weeConfigFile fails to find entries defined in included configuration files.'));

	$o = new weeConfigFile_include();
	$o->parseFile(dirname(__FILE__) . '/condinclude.cnf');
	$this->isEqual('1', $o->aConfig['test'],
		_WT('weeConfigFile fails to include files conditionally.'));
}
catch(FileNotFoundException $e)
{
	$this->fail(_WT('weeConfigFile throws an UnexpectedValueException when the chain of includes is correct.'));
}
