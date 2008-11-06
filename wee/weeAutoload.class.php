<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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

	When using caching by configuring WEE_AUTOLOAD_CACHE, please ensure that you give
	the complete path to the file, as the file is saved in a shutdown function and
	under certain servers (e.g. Apache), the working directory can change at that point.

	For example, instead of using:
		define('WEE_AUTOLOAD_CACHE', 'app/tmp/autoload.php');
	Use:
		define('WEE_AUTOLOAD_CACHE', getcwd() . '/app/tmp/autoload.php');

	The cache file is disabled in DEBUG mode.
*/

final class weeAutoload
{
	/**
		Maps all the classes to their filenames.
	*/

	protected static $aPaths = array();

	/**
		List of paths already loaded.
		Contains the $sPath argument given to weeAutoload::addPath.
	*/

	protected static $aPathsLoaded = array();

	/**
		Namespace.
	*/

	private function __construct()
	{
	}

	/**
		Adds a path to autoload from.

		You must tell weeAutoload which paths contains the files to autoload.
		This function will stores all the filenames ending with CLASS_EXT, for later use.

		When the path is already loaded, this function will not reload it.
		If you are in a development environment and the cache is activated
		(by defining WEE_AUTOLOAD_CACHE) in your project, you can make the
		WEE_AUTOLOAD_CACHE file read-protected to prevent the use of the cache.

		@param $sPath The path to autoload from.
	*/

	public static function addPath($sPath)
	{
		if (in_array($sPath, self::$aPathsLoaded))
			return;

		$oDir = new RecursiveDirectoryIterator($sPath);
		foreach (new RecursiveIteratorIterator($oDir) as $oFilename)
		{
			if (substr($oFilename, -strlen(CLASS_EXT)) != CLASS_EXT)
				continue;

			self::$aPaths[basename($oFilename, CLASS_EXT)] = (string)$oFilename;
		}

		self::$aPathsLoaded[] = $sPath;
	}

	/**
		Autoloads the specified class, if it's in the autoload paths.
		You should never need to call this function yourself.

		@param $sClass The class to autoload.
	*/

	public static function loadClass($sClass)
	{
		if (!empty(self::$aPaths[$sClass]))
			require(self::$aPaths[$sClass]);
	}

	/**
		Load the autoload data from the specified cache file.
		Overwrites any existing autoload data.

		@param $sFilename The autoload cache filename.
	*/

	public static function loadFromCache($sFilename)
	{
		require($sFilename);
	}

	/**
		Save the autoload data to the specified cache file.

		The cache file is just PHP code that will get executed at load.
		It contains code to set the values to weeAutoload::$aPaths and weeAutoload::$aPathsLoaded.

		@param $sFilename The autoload cache filename.
	*/

	public static function saveToCache($sFilename)
	{
		$sCache = '<?php self::$aPaths = '
			. var_export(self::$aPaths, true)
			. '; self::$aPathsLoaded = '
			. var_export(self::$aPathsLoaded, true)
			. ';';

		file_put_contents($sFilename, $sCache);
		chmod($sFilename, 0600);
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

// Handle cache loading and saving

if (!defined('DEBUG') && defined('WEE_AUTOLOAD_CACHE') && !defined('NO_CACHE'))
{
	if (is_readable(WEE_AUTOLOAD_CACHE))
		weeAutoload::loadFromCache(WEE_AUTOLOAD_CACHE);
	else
		register_shutdown_function(array('weeAutoload', 'saveToCache'), WEE_AUTOLOAD_CACHE);
}
