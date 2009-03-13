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

/**
	Interface for caching drivers.
*/

interface weeCache extends ArrayAccess
{
	/**
		Initialize the cache driver.

		@param $aParams Parameters used to configure the driver.
	*/

	public function __construct($aParams = array());

	/**
		Clear the cache.
	*/

	public function clear();

	/**
		Store a value only if it doesn't already exists and fail otherwise.

		@param $sKey The key to create.
		@param $mValue The value that will be cached.
		@param $iTTL Time to live, in seconds.
	*/

	public function create($sKey, $mValue, $iTTL = 0);

	/**
		Retrieve multiple keys simultaneously.

		@param $aKeys The keys to retrieve.
		@return array The associative array containing the values retrieved.
	*/

	public function getMulti($aKeys);

	/**
		Store a value. Overwrite the existing one if any.

		@param $sKey The key to store.
		@param $mValue The value that will be cached.
		@param $iTTL Time to live, in seconds.
	*/

	public function store($sKey, $mValue, $iTTL = 0);
}
