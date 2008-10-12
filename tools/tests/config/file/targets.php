<?php

class weeFileConfig_isTargetedSystem extends weeFileConfig
{
	// We need to instantiate this class without a specified configuration 
	// file, so we create a dummy constructor.
	public function __construct() {}

	// This is the method we're trying to test, let's expose it.
	public function isTargetedSystem($sInstruction)
	{
		return parent::isTargetedSystem($sInstruction);
	}

	// We override default targets to make the following tests more 
	// "update-proof"
	public function getTargetFunctions($bGetParentTargets = false)
	{
		if ($bGetParentTargets)
			return parent::getTargetFunctions();

		static $aFunc = array(
			'os'		=> 'php_uname("s")',
			'extver'	=> 'phpversion(":1")'
		);

		return $aFunc;
	}
}

$o = new weeFileConfig_isTargetedSystem();

try
{
	$o->isTargetedSystem('$(invalid_instruction_without_a_closing_parenthesis');
	$this->fail('weeFileConfig does not throw an UnexpectedValueException when the specified instruction is malformed.');
}
catch (UnexpectedValueException $e) {}

try
{
	$o->isTargetedSystem('$(instruction_which_does_not_exist)');
	$this->fail('weeFileConfig does not throw an UnexpectedValueException when the function does not exist.');
}
catch (UnexpectedValueException $e) {}

try
{
	$o->isTargetedSystem('$(os)');
	$this->fail('weeFileConfig does not throw an UnexpectedValueException when the target is missing.');
}
catch (UnexpectedValueException $e) {}

try
{
	$o->isTargetedSystem('$(extver 1.0)');
	$this->fail('weeFileConfig does not throw an UnexpectedValueException when the function lacks a parameter.');
}
catch (UnexpectedValueException $e) {}

foreach ($o->getTargetFunctions(true) as $sFunction => $sEval)
{
	$sFunctionCall = preg_replace('/:\d+/', '"\0"', $sEval) . ';';
	$i = 0;

	// Swallow the system() call output...
	ob_start();
	system('echo ' . escapeshellarg($sFunctionCall) . ' | php -l', $i);
	// ...and then discard it.
	ob_end_clean();

	$this->isEqual(0, $i, "Builtin weeFileConfig '$sFunction' target is not a valid PHP function call.");
}

try
{
	$this->isTrue($o->isTargetedSystem('$(os ' . php_uname('s') . ')'),
		'weeFileConfig fails to see that the targeted system is the one currently used.');

	$this->isFalse($o->isTargetedSystem('$(os os_which_is_not_the_one_currently_used)'),
		'weeFileConfig fails to see that the targeted system is not the one currently used.');

	// Special test for "Windows NT" which is in two words instead of one
	$this->isTrue(defined('WEE_ON_WINDOWS') && $o->isTargetedSystem('$(os Windows NT)'),
		'weeFileConfig fails when the operating system name uses two words.');
}
catch (UnexpectedValueException $e)
{
	$this->fail('weeFileConfig throws an UnexpectedValueException when the given instruction is valid.');
}
