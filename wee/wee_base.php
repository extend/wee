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

	Original author: vboedefeld at googlemail dot com on PHP comments for rmdir.
	Also thanks to all the contributors before him.

	@param $sPath Path to the directory to remove.
*/

function rmdir_recursive($sPath)
{
	foreach (glob($sPath . '/*') as $sFile)
	{
		if (is_dir($sFile) && !is_link($sFile))
			rmdir_recursive($sFile);
		else
			@unlink($sFile);
	}

	@rmdir($sPath);
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
