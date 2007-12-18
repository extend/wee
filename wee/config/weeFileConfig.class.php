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
		Load the specified configuration file.

		@param $sFilename Path and filename to the configuration file
	*/

	public function __construct($sFilename)
	{
		$rFile = fopen($sFilename, 'r');
		fire($rFile === false, 'FileNotFoundException', "Cannot open file '" . $sFilename . "'.");

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
		fire(strpos($sInstruction, ')') === false, 'UnexpectedValueException',
			'The targeted system instruction is missing the closing parenthese.');

		$sInstruction = substr($sInstruction, 0, strpos($sInstruction, ')'));

		//TODO:maybe makes this array inheritable in a way or another
		static $aFunc = array(
			'os'		=> 'php_uname("s")',
			'host'		=> 'php_uname("n")',
			'phpver'	=> 'phpversion()',
			'extver'	=> 'phpversion(":1")',
			'sapi'		=> 'php_sapi_name()',
			'path'		=> 'dirname(dirname(dirname(__FILE__)))',
		);

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
}

?>
