<?php

/*
	Web:Extend
	Copyright (c) 2008 Dev:Extend

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
	Represents a gettext dictionary.
*/

class weeGetTextDictionary
{
	/**
		The header dictionary.
	*/

	private $aHeaders;

	/**
		The associative array of the strings contained in the gettext dictionary.
	*/

	private $aStrings;

	/**
		The plural forms count.
	*/

	private $iPluralFormsCount;

	/**
		The plural form index function.
	*/

	private $mPluralFormIndexFunction;

	/**
		The charset of the strings in the dictionary.
	*/

	private $sCharset;

	/**
		Constructs a new gettext dictionary.
		@param $sFilename The name of the binary MO file containing the dictionary.
	*/

	public function __construct($sFilename)
	{
		$oReader = new weeGetTextReader($sFilename);
		$this->aStrings = $oReader->getStrings();
	}

	/**
		Returns the charset of the strings in the dictionary.
		@return string		The charset of the strings.
	*/

	public function getCharset()
	{
		if ($this->sCharset === null)
		{
			$aHeaders = $this->getHeaders();
			if (isset($aHeaders['Content-Type']))
				for ($s = strtok($aHeaders['Content-Type'], ';'); $s !== false; $s = strtok(';'))
				{
					$s = trim($s);
					$i = strpos($s, '=');
					if ($i !== false && rtrim(substr($s, 0, $i)) == 'charset')
					{
						$this->sCharset = ltrim(substr($s, $i + 1));
						break;
					}
				}
		}

		return $this->sCharset;
	}

	/**
		Returns the headers of the dictionary.
		@return	array		The array of headers.
	*/

	public function getHeaders()
	{
		if ($this->aHeaders === null)
		{
			$this->aHeaders = array();

			if (isset($this->aStrings['']))
				for ($s = strtok($this->aStrings[''], "\n"); $s !== false; $s = strtok("\n"))
				{
					$s = trim($s);
					$i = strpos($s, ':');
					$this->aHeaders[rtrim(substr($s, 0, $i))] = ltrim(substr($s, $i + 1));
				}
		}

		return $this->aHeaders;
	}

	/**
		Returns the translation of a given string, if any.
		@param	$sString	The string to be translated.
		@return	string		The translation of the string.
	*/

	public function getTranslation($sString)
	{
		return array_value($this->aStrings, $sString, $sString);
	}

	/**
		Returns the index of the plural form to use for the given number.
		@param	$i	The given number.
		@return	int	The index of the plural form to use.
	*/

	private function getPluralFormIndex($i)
	{
		if ($this->mPluralFormIndexFunction === null)
		{
			$this->iPluralFormsCount		= 2;
			$this->mPluralFormIndexFunction	= array($this, 'getPluralFormIndexDefault');

			$aHeaders = $this->getHeaders();
			if (isset($this->aHeaders['Plural-Forms']))
			{
				for ($s = strtok($this->aHeaders['Plural-Forms'], ';'); $s !== false; $s = strtok(';'))
				{
					$s = trim($s);
					$i = strpos($s, '=');
					if ($i !== false)
						$a[rtrim(substr($s, 0, $i))] = ltrim(substr($s, $i + 1));
				}

				$this->iPluralFormsCount		= (int)$a['nplurals'];
				$this->mPluralFormIndexFunction	= create_function('$i',
					'return (int)(' . str_replace('n', '$i', $a['plural']) . ');');
			}
		}

		$i = call_user_func($this->mPluralFormIndexFunction, $i);
		($i < 0 || $i >= $this->iPluralFormsCount) and burn('UnexceptedValueException',
			$i . ' is not a valid plural form index.');
		return $i;
	}

	/**
		Default implementation of getPluralForm().
		@see getPluralForm()
	*/

	private function getPluralFormIndexDefault($i)
	{
		return $i > 1 ? 1 : 0;
	}

	/**
		Returns the translation of a given string and its plural form.
		@param	$i		The number which is used to check which translated plural form should be used.
		@return	string	The translation of the string, if any.
	*/

	public function getPluralTranslation($sString, $sPluralString, $i)
	{
		$sKey = $sString . "\0" . $sPluralString;

		if (!isset($this->aStrings[$sKey]))
			return $this->getPluralFormIndexDefault($i) ? $sPluralString : $sString;

		if (is_string($this->aStrings[$sKey]))
			$this->aStrings[$sKey] = explode("\0", $this->aStrings[$sKey]);

		$iPluralForm = $this->getPluralFormIndex($i);
		isset($this->aStrings[$sKey][$iPluralForm]) or burn('UnexceptedValueException',
			$iPluralForm . ' is not a valid plural form index for the given strings.');
		return $this->aStrings[$sKey][$iPluralForm];
	}
}
