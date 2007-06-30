<?php

if (!defined('ALLOW_INCLUSION')) die;

/**
	Loader of Web:Extend's .cnf configuration files.

	@see share/conf/sample.cnf for an example configuration file
*/

class weeFileConfig extends weeConfig
{
	/**
		Load the specified configuration file.

		@param $sFilename Path and filename to the configuration file
	*/

	public function __construct($sFilename)
	{
		$rFile = fopen($sFilename, 'r');
		fire($rFile === false);

		while (!feof($rFile))
		{
			$sLine = trim(fgets($rFile, 1024));

			if (empty($sLine) || $sLine[0] == '#')
				continue;

			$sLeft	= rtrim(substr($sLine, 0, strpos($sLine, '=')));
			$sRight	= ltrim(substr($sLine, strpos($sLine, '=') + 1));

			if (substr($sLeft, 0, 2) == '$(')
			{
				if ($this->isTargetedSystem($sLeft))
					$sLeft = substr($sLeft, strpos($sLeft, ').') + 2);
				else
					continue;
			}
			//TODO: .include file

			$this->aConfig[$sLeft] = $sRight;
		}

		fclose($rFile);
	}

	/**
		Check if the system targeted is the same as the one currently used.

		Configuration lines can begin with a $(instruction). command.
		The 'instruction' is a list of words, following this schema:
			function [param1] [param2] [...] target

		With function one of these:
			os:		Operating System name, e.g. NetBSD.
			host:	Hostname, like localhost.example.com.
			phpver:	PHP version.
			extver:	PHP extension version. Needs one parameter: the extension's name.
			sapi:	Type of interface between web server and PHP.

		And target is the value wanted.

		@param	$sInstruction	The 'instruction' string
		@return	bool			Whether this system is the targeted system
		@warning				The value wanted must not have spaces
		@todo More targets
	*/

	protected function isTargetedSystem($sInstruction)
	{
		$sInstruction = substr($sInstruction, 2);
		fire(strpos($sInstruction, ')') === false);

		$sInstruction = substr($sInstruction, 0, strpos($sInstruction, ')'));

		static $aFunc = array(
			'os'		=> 'php_uname("s")',
			'host'		=> 'php_uname("n")',
			'phpver'	=> 'phpversion()',
			'extver'	=> 'phpversion(":1")',
			'sapi'		=> 'php_sapi_name()',
		);

		$aWords = explode(' ', $sInstruction);
		fire(empty($aFunc[$aWords[0]]));

		$sEval = $aFunc[$aWords[0]];
		fire(sizeof($aWords) != 2 + (strpos($sEval, ':') !== false));

		for ($i = 1; $i < sizeof($aWords) - 1; $i++)
			$sEval = str_replace(':' . $i, addslashes($aWords[$i]), $sEval);

		$sResult = eval('return ' . $sEval . ';');

		return ($sResult == $aWords[sizeof($aWords) - 1]);
	}
}

?>
