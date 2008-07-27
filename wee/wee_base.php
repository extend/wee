<?php

/*
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
	Returns the array value if it exists, else a default value.
	Simpler form than using the conditional operators, and returns null by default, which we usually want.

	@param	$aArray		The array.
	@param	$sKey		The key to look for in the array.
	@param	$mIfNotSet	The default value.
	@return	mixed
*/

function array_value($aArray, $sKey, $mIfNotSet = null)
{
	if (isset($aArray[$sKey]))
		return $aArray[$sKey];
	return $mIfNotSet;
}

/**
	Format a string to an HTML unsorted list.
	Each line of the string (with \r\n separator) becomes a line of the list.

	@param	$s		The string to be formatted.
	@return	string	The string formatted to an HTML unsorted list.
*/

function nl2uli($s)
{
	if (empty($s))
		return $s;

	if (substr($s, strlen($s) - 2) == "\r\n")
		$s = substr($s, 0, strlen($s) - 2);

	$s = str_replace("\r\n", '</li><li>', $s);
	return '<ul><li>' . $s . '</li></ul>';
}

/**
	Remove a directory and all its contents.

	@param	$sPath					Path to the directory to remove.
	@param	$bOnlyContents			Boolean to check if the directory is to be left in place.
	@throw	FileNotFoundException	$sPath is not a directory.
	@throw	NotPermittedException	$sPath cannot be removed because of insufficient file permissions.
*/

function rmdir_recursive($sPath, $bOnlyContents = false)
{
	fire(!is_dir($sPath), 'FileNotFoundException',
		"'$sPath' is not a directory.");

	$r = @opendir($sPath);
	fire($r === false, 'NotPermittedException', "'$sPath' directory cannot be opened.");

	while (($s = readdir($r)) !== false)
		if ($s != '.' && $s !== '..')
		{
			$s = $sPath . '/' . $s;
			if (is_dir($s) && !is_link($s))
				rmdir_recursive($s);
			else
			{
				$b = @unlink($s);
				fire(!$b, 'NotPermittedException', "'$s' file cannot be deleted.");
			}
		}

	closedir($r);

	if (!$bOnlyContents)
	{
		$b = @rmdir($sPath);
		fire(!$b, 'NotPermittedException', "'$sPath' directory cannot be deleted.");
	}
}

/**
	Convert special characters to HTML entities.

	Original author: treyh on PHP comments for htmlspecialchars.

	@param $sText The string being converted.
	@return string The converted string.
*/

function xmlspecialchars($sText)
{
	return str_replace('&#039;', '&apos;', htmlspecialchars($sText, ENT_QUOTES, 'utf-8'));
}

/**
	PHP namespace emulation.
	Namespaces should be declared as final.
*/

class Namespace { private function __construct() {} }

/**
	Interface for declaring singletons in wee.
*/

interface Singleton { public static function instance(); }

/**
	Interface for printable objects.
*/

interface Printable { public function toString(); }

/**
	Base class for data source objects.
	These object are required to encode the data when needed.

	Use weeOutput::encodeValue or weeOutput::encodeArray to encode it.
*/

abstract class weeDataSource
{
	/**
		Whether to automatically encode the data before returning it.
	*/

	protected $bMustEncodeData = false;

	/**
		Tells the object to automatically encode the data before returning it.

		@return $this
	*/

	public function encodeData()
	{
		$this->bMustEncodeData = true;
		return $this;
	}
}
