<?php

/*
	Web:Extend
	Copyright (c) 2006 Dev:Extend

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
	Namespace for class autoloading.

	@todo Cached version.
*/

final class weeAutoload extends Namespace
{
	/**
		Maps all the classes to their filenames.
	*/

	protected static $aPaths = array();

	/**
		Adds a path to autoload from.

		You must tell weeAutoload which paths contains the files to autoload.
		This function will stores all the filenames ending with CLASS_EXT, for later use.

		@param $sPath The path to autoload from.
	*/

	public static function addPath($sPath)
	{
		$oDir = new RecursiveDirectoryIterator($sPath);
		foreach (new RecursiveIteratorIterator($oDir) as $oFilename)
		{
			if (substr($oFilename, -strlen(CLASS_EXT)) != CLASS_EXT)
				continue;

			self::$aPaths[basename($oFilename, CLASS_EXT)] = (string)$oFilename;
		}
	}

	/**
		Autoloads the specified class, if it's in the autoload paths. Else throws a FileNotFoundException.
		You should never need to call this functions yourself.

		@param $sClass The class to autoload.
	*/

	public static function loadClass($sClass)
	{
		fire(empty(self::$aPaths[$sClass]), 'FileNotFoundException');
		require(self::$aPaths[$sClass]);
	}
}

// Register autoload functions

if (function_exists('spl_autoload_register'))
{
	ini_set('unserialize_callback_func', 'spl_autoload_call');
	spl_autoload_register(array('weeAutoload', 'loadClass'));
	if (function_exists('__autoload'))
		spl_autoload_register('__autoload');
}
elseif (!function_exists('__autoload'))
{
	ini_set('unserialize_callback_func', '__autoload');
	function __autoload($sClass)
	{
		weeAutoload::loadClass($sClass);
	}
}

?>
