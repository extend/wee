<?php

/**
	Web:Extend
	Copyright (c) 2007 Dev:Extend

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ALLOW_INCLUSION')) die;

/**
	Loader of Web:Extend's .cnf configuration files.

	@see share/conf/sample.cnf for an example configuration file
*/

class weeFileConfig extends weeConfig
{
	/**
		The stack of the files being currently parsed.

		Used to .include other configuration files inside themselves.
	*/

	protected $aFilesStack = array();

	/**
		Load the specified configuration file.

		@param $sFilename Path and filename to the configuration file
	*/

	public function __construct($sFilename)
	{
		$this->parseFile($sFilename);
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
		fire(strpos($sInstruction, ')') === false, 'UnexpectedValueException',
			'The targeted system instruction is missing the closing parenthese.');

		$sInstruction = substr($sInstruction, 0, strpos($sInstruction, ')'));

		$aFunc = $this->getTargetFunctions();
		
		$aWords = explode(' ', $sInstruction);
		fire(empty($aFunc[$aWords[0]]), 'UnexpectedValueException',
			'The targeted system instruction ' . $aWords[0] . ' do not exist.');

		$sEval = $aFunc[$aWords[0]];
		fire(sizeof($aWords) != 2 + (strpos($sEval, ':') !== false), 'UnexpectedValueException',
			'The targeted system instruction should have ' . (2 + (strpos($sEval, ':') !== false)) .
			' parameters, ' . sizeof($aWords) . ' were given.');

		$iNbArgs = substr_count($sEval, ':');
		$sWanted = '';

		for ($i = 1 + $iNbArgs; $i < sizeof($aWords); $i++)
			$sWanted .= $aWords[$i] . ' ';
		$sWanted = substr($sWanted, 0, -1);

		for ($i = 1; $i <= 1 + $iNbArgs; $i++)
			$sEval = str_replace(':' . $i, addslashes($aWords[$i]), $sEval);

		$sResult = eval('return ' . $sEval . ';');

		return ($sResult == $sWanted);
	}

	/**
		Parse the specified configuration file.

		@param $sFilename Path and filename to the configuration file
	*/

	protected function parseFile($sFilename)
	{
		fire(!file_exists($sFilename), 'FileNotFoundException',
			"File '$sFilename' does not exist.");
		
		$sRealpath = realpath($sFilename);
		fire(in_array($sRealpath, $this->aFilesStack), 'UnexpectedValueException',
			"'$sRealpath' configuration file is already in the parsing stack.");

		$rFile					= fopen($sFilename, 'r');
		$this->aFilesStack[]	= $sRealpath;

		while (!feof($rFile))
		{
			$sLine = trim(fgets($rFile));

			if (empty($sLine) || $sLine[0] == '#')
				continue;

			if (substr($sLine, 0, 2) == '$(')
			{
				if (!$this->isTargetedSystem($sLine))
					continue;

				$sLine = ltrim(substr($sLine, strpos($sLine, ').') + 2));
			}

			if (substr($sLine, 0, 7) == 'include')
			{
				$this->parseFile($this->getIncludeFilename(ltrim(substr($sLine, 7))));
				continue;
			}

			$sLeft	= rtrim(substr($sLine, 0, strpos($sLine, '=')));
			$sRight	= ltrim(substr($sLine, strpos($sLine, '=') + 1));

			$this->aConfig[$sLeft] = $sRight;
		}

		fclose($rFile);
		array_pop($this->aFilesStack);
	}

	/**
		Return the filename of the configuration file which is to be included.

		If the path of the configuration file begins with "//" the path is relative to ROOT_PATH,
		if it begins with "./", then it is relative to the current file being parsed,
		otherwise the standard behaviour is adopted, working directory being the one of the process.

		@param	$sPath	The path of the configuration file.
		@return	string	The filename of the configuration file.
	*/

	public function getIncludeFilename($sPath)
	{
		switch (substr($sPath, 0, 2))
		{
			case '//':
				return ROOT_PATH . substr($sPath, 2);
				break;

			case './':
				return dirname($this->aFilesStack[sizeof($this->aFilesStack) - 1]) . '/' . substr($sPath, 2);
				break;
		}

		return $sPath;
	}

	/**
		Return the table of target functions.

		@return array The table of the functions supported by the class in targets.
	*/

	protected function getTargetFunctions()
	{
		static $aFunc = array(
			'os'		=> 'php_uname("s")',
			'host'		=> 'php_uname("n")',
			'phpver'	=> 'phpversion()',
			'extver'	=> 'phpversion(":1")',
			'sapi'		=> 'php_sapi_name()',
			'path'		=> 'dirname(dirname(dirname(__FILE__)))',
		);

		return $aFunc;
	}
}

