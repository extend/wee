<?php

/**
	Web:Extend
	Copyright (c) 2006-2009 Dev:Extend

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

class weeConfigFile implements Mappable
{
	/**
		Contains the configuration data.
	*/

	protected $aConfig = array();

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
		Cache-aware configuration loading.

		If a cache file is available, load directly the configuration array from the cache.
		Otherwise, create a weeConfigFile object, retrieve the configuration as an array,
		save that array into a cache file and return it.

		No cached data is loaded if DEBUG or NO_CACHE is defined.

		@param $sFilename The configuration file's filename.
		@param $sCacheFilename The configuration file's cache filename.
		@return array The configuration data that has been loaded.
	*/

	public static function cachedLoad($sFilename, $sCacheFilename)
	{
		// Load from the cache if possible

		if (!defined('DEBUG') && !defined('NO_CACHE') && is_readable($sCacheFilename))
			return require($sCacheFilename);

		// Otherwise try to load the configuration file

		$oConfigFile = new weeConfigFile($sFilename);
		$aConfig = $oConfigFile->toArray();

		// Configuration file has been loaded, cache it for later if possible

		if (!defined('DEBUG')) {
			file_put_contents($sCacheFilename, '<?php return ' . var_export($aConfig, true) . ';');
			chmod($sCacheFilename, 0600);
		}

		return $aConfig;
	}

	/**
		Return the filename of the configuration file which is to be included.

		If the path of the configuration file begins with {{{ "//" }}} the path is relative to ROOT_PATH,
		if it begins with "./", then it is relative to the current file being parsed,
		otherwise the standard behaviour is adopted, working directory being the one of the process.

		@param	$sPath	The path of the configuration file.
		@return	string	The filename of the configuration file.
	*/

	protected function getIncludeFilename($sPath)
	{
		$sPrefix = substr($sPath, 0, 2);

		if ($sPrefix == '//')
			return ROOT_PATH . substr($sPath, 2);

		if ($sPrefix == './')
			return dirname(end($this->aFilesStack)) . '/' . substr($sPath, 2);

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

	/**
		Check if the system targeted is the same as the one currently used.

		Configuration lines can begin with a $(instruction). command.
		The 'instruction' is a list of words, following this schema:
			* function [param1] [param2] [...] target

		With function one of these:
			* os:		Operating System name, e.g. NetBSD.
			* host:	Hostname, like localhost.example.com.
			* phpver:	PHP version.
			* extver:	PHP extension version. Needs one parameter: the extension's name.
			* sapi:	Type of interface between web server and PHP.

		And target is the value wanted.

		If you need to group some words to form the parameters, surround them with double quotes.

		@param	$sInstruction	The 'instruction' string
		@return	bool			Whether this system is the targeted system
		@warning				The function name must not have spaces
		@todo More targets
	*/

	protected function isTargetedSystem($sInstruction)
	{
		$sInstruction	= substr($sInstruction, 2);
		$i				= strpos($sInstruction, ')');

		$i !== false
			or burn('UnexpectedValueException',
				_WT('The targeted system instruction is missing the closing parenthese.'));

		$sInstruction	= trim(substr($sInstruction, 0, $i));
		$aFuncs			= $this->getTargetFunctions();
		$i				= strpos($sInstruction, ' ');

		$i !== false
			or burn('UnexpectedValueException',
				_WT('The instruction does not have a wanted value.'));

		$i != 0
			or burn('UnexpectedValueException',
				_WT('The instruction does not have a target function.'));

		$sFunction = substr($sInstruction, 0, $i);
		isset($aFuncs[$sFunction])
			or burn('UnexpectedValueException',
				sprintf(_WT('The target function "%s" does not exist.'), $sFunction));

		$sInstruction	= substr($sInstruction, $i + 1);
		$aArgs			= array();
		while ($sInstruction)
		{
			if ($sInstruction[0] == '"')
			{
				$sInstruction	= substr($sInstruction, 1);
				$i				= strpos($sInstruction, '"');

				$i !== false
					or burn('UnexpectedValueException',
						_WT('A closing double quote is missing.'));
			}
			else
			{
				$i = strpos($sInstruction, ' ');
				if ($i === false)
				{
					$aArgs[] = $sInstruction;
					break;
				}
			}

			$aArgs[]		= substr($sInstruction, 0, $i);
			$sInstruction	= substr($sInstruction, $i + 1);
		}

		$sWanted		= array_pop($aArgs);
		$sEval			= $aFuncs[$sFunction];
		$iExpectedArgs	= substr_count($sEval, ':');
		$iActualArgs	= count($aArgs);

		$iExpectedArgs == $iActualArgs
			or burn('UnexpectedValueException',
				sprintf(_WT('The target function expects %d arguments but %d were given.'), $iExpectedArgs, $iActualArgs));

		foreach ($aArgs as $i => $sArg)
			$sEval = str_replace(':' . $i, addslashes($sArg), $sEval);

		return eval('return ' . $sEval . ';') == $sWanted;
	}

	/**
		Parse the specified configuration file.

		@param $sFilename Path and filename to the configuration file.
	*/

	protected function parseFile($sFilename)
	{
		file_exists($sFilename) or burn('FileNotFoundException',
			sprintf(_WT('The file "%s" does not exist.'), $sFilename));
		
		$sRealpath = realpath($sFilename);
		in_array($sRealpath, $this->aFilesStack) and burn('UnexpectedValueException',
			sprintf(_WT('The configuration file "%s" is already in the parsing stack. There may be a recursive inclusion.'), $sRealpath));

		$rFile					= fopen($sFilename, 'r');
		$this->aFilesStack[]	= $sRealpath;

		while (!feof($rFile))
			$this->parseLine(fgets($rFile));

		fclose($rFile);
		array_pop($this->aFilesStack);
	}

	/**
		Parse a configuration line.

		@param $sLine The configuration line.
	*/

	protected function parseLine($sLine)
	{
		$sLine = trim($sLine);

		// Empty lines and comments
		if (empty($sLine) || $sLine[0] == '#')
			return;

		// Target instructions
		if (substr($sLine, 0, 2) == '$(')
		{
			if (!$this->isTargetedSystem($sLine))
				return;

			$sLine = ltrim(substr($sLine, strpos($sLine, ').') + 2));
		}

		// Inclusions
		if (substr($sLine, 0, 7) == 'include')
		{
			$sParam = ltrim(substr($sLine, 7));

			empty($sParam) and burn('UnexpectedValueException',
				_WT('The parameter of the include instruction is missing.'));

			if ($sParam[0] != '=')
			{
				$this->parseFile($this->getIncludeFilename(ltrim(substr($sLine, 7))));
				return;
			}
		}

		$i = strpos($sLine, '=');
		$i === false and burn('UnexpectedValueException',
			_WT('The assignement instruction does not have an equal sign.'));

		$sLeft	= rtrim(substr($sLine, 0, $i));
		$sRight	= ltrim(substr($sLine, $i + 1));

		$this->aConfig[$sLeft] = $sRight;
	}

	/**
		Returns the data as array, since we can't cast weeConfigFile to retrieve the array's data.

		@return array Object's data.
	*/

	public function toArray()
	{
		return $this->aConfig;
	}
}
