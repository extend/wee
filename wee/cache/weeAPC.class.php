<?php

/*
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

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
	APC caching driver.

	@see http://php.net/apc
*/

class weeAPC implements weeCache
{
	/**
		Process-specific cache. All operations are cached
		in this array to prevent querying the cache if not necessary.
	*/

	protected $aData = array();

	/**
		Initialize the cache driver.
		The APC caching driver has no parameter.

		@param $aParams Parameters used to configure the driver.
	*/

	public function __construct($aParams = array())
	{
	}

	/**
		Clear the cache.
	*/

	public function clear()
	{
		apc_clear_cache('user') or burn('CacheException',
			_WT('An error occurred while trying to clear the cache.'));

		$this->aData = array();
	}

	/**
		Store a value only if it doesn't already exists and fail otherwise.

		@param $sKey The key to create.
		@param $mValue The value that will be cached.
		@param $iTTL Time to live, in seconds.
	*/

	public function create($sKey, $mValue, $iTTL = 0)
	{
		apc_add($sKey, serialize($mValue), $iTTL) or burn('CacheException',
			sprintf(_WT('The key "%s" you tried to create probably already exists.'), $sKey));

		$this->aData[$sKey] = $mValue;
	}

	/**
		Retrieve multiple keys simultaneously.

		@param $aKeys The keys to retrieve.
		@return array The associative array containing the values retrieved.
	*/

	public function getMulti($aKeys)
	{
		$bSuccess = true;
		$a = apc_fetch($aKeys, $bSuccess);

		$bSuccess or burn('CacheException',
			_WT('An error occurred while trying to retrieve an array of keys.'));

		foreach ($a as &$m)
			$m = unserialize($m);

		return $a;
	}

	/**
		Returns whether the key exists in the cache.

		@param $sKey The key to check.
		@return bool Whether the key exists.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetExists($sKey)
	{
		if (!array_key_exists($sKey, $this->aData)) {
			$bSuccess = true;
			$m = apc_fetch($sKey, $bSuccess);

			if ($m === false || $bSuccess === false)
				return false;

			$this->aData[$sKey] = unserialize($m);
			return true;
		}

		return true;
	}

	/**
		Retrieve the value for the given key.

		@param $sKey The key to retrieve.
		@return mixed Value retrieved from the cache.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetGet($sKey)
	{
		if (array_key_exists($sKey, $this->aData))
			return $this->aData[$sKey];

		$bSuccess = true;
		$m = apc_fetch($sKey, $bSuccess);

		$bSuccess or burn('CacheException',
			sprintf(_WT('An error occurred while trying to retrieve the key "%s".'), $sKey));

		$m === false and burn('CacheException',
			sprintf(_WT('The key "%s" does not exist.'), $sKey));

		$m = unserialize($m);
		$this->aData[$sKey] = $m;

		return $m;
	}

	/**
		Store a value. Overwrite the existing one if any.

		@param $sKey The key to store.
		@param $mValue The value that will be cached.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetSet($sKey, $mValue)
	{
		$this->store($sKey, $mValue);
	}

	/**
		Delete a key.

		@param $sKey The key to delete.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetUnset($sKey)
	{
		apc_delete($sKey) or burn('CacheException',
			sprintf(_WT('An error occurred while trying to delete the key "%s".'), $sKey));

		unset($this->aData[$sKey]);
	}

	/**
		Store a value. Overwrite the existing one if any.

		@param $sKey The key to store.
		@param $mValue The value that will be cached.
		@param $iTTL Time to live, in seconds.
	*/

	public function store($sKey, $mValue, $iTTL = 0)
	{
		apc_store($sKey, serialize($mValue), $iTTL) or burn('CacheException',
			sprintf(_WT('An error occurred while trying to store the key "%s".'), $sKey));

		$this->aData[$sKey] = $mValue;
	}
}
