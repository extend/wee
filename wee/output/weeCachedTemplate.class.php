<?php

/*
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

if (!defined('CACHE_EXPIRE'))	define('CACHE_EXPIRE',	300);
if (!defined('CACHE_PATH'))		define('CACHE_PATH',	ROOT_PATH . 'app/tmp/appcache/');

if (!is_dir(CACHE_PATH))
	mkdir(CACHE_PATH, 0700, true);

/**
	Template handling, cache aware version.
	Load, configure and display templates.
*/

class weeCachedTemplate extends weeTemplate
{
	/**
		True if this template is cached, false otherwise.
		This is a cache of the result of isCached.
	*/

	protected $bCached = false;

	/**
		Cache filename.
	*/

	protected $sCacheFile;

	/**
		Configure the filename and the data for this template.
		Also automatically configure the path to the cache file.

		@param $sTemplate	The template name.
		@param $aData		Data to be used in the template.
	*/

	public function __construct($sTemplate, array $aData = array())
	{
		parent::__construct($sTemplate, $aData);
		$this->sCacheFile = CACHE_PATH . md5($_SERVER['REQUEST_URI'] . $sTemplate);
	}

	/**
		Checks if the template has a corresponding cache file,
		and if this cache is not too old.

		@return bool True if a valid cache file exists, false otherwise.
	*/

	public function isCached()
	{
		if (defined('NO_CACHE'))
			return false;

		if ($this->bCached)
			return true;

		if (!file_exists($this->sCacheFile))
			return false;

		$iTime = filemtime($this->sCacheFile);

		if ($iTime === false)
			return false;

		if ($iTime + CACHE_EXPIRE < time())
			return false;

		$this->bCached = true;
		return true;
	}

	/**
		Returns the template as a string.
		If the file is cached, returns the cache of the file.

		@return string The template.
	*/

	public function toString()
	{
		if ($this->isCached())
			return file_get_contents($this->sCacheFile);

		$sContents = parent::toString();

		if (!defined('NO_CACHE'))
		{
			file_put_contents($this->sCacheFile, $sContents);
			chmod($sCacheFilename, 0600);
		}

		return $sContents;
	}
}
