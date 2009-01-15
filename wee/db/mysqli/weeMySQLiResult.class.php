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
	Class for MySQLi query results handling.

	Instances of this class are returned by weeMySQLiDatabase's query method and
	should not be instantiated manually.
*/

class weeMySQLiResult extends weeDatabaseResult
{
	/**
		The mysqli result set.
	*/

	protected $oResult;

	/**
		Initialises a new mysqli result set.

		@param	$oResult	The mysqli result set.
	*/

	public function __construct(mysqli_result $oResult)
	{
		$this->oResult = $oResult;
	}

	/**
		Return the number of results returned by the query.

		@return	int	The number of results.
	*/

	public function count()
	{
		return $this->oResult->num_rows;
	}

	/**
		Fetches the data of the next row of the result set.

		@return	mixed	An array containing the data of the next row or false if there is no current row.
	*/

	protected function doFetch()
	{
		$m = $this->oResult->fetch_assoc();
		return $m !== null ? $m : false;
	}

	/**
		Rewinds the result set to its first row.
	*/

	protected function doRewind()
	{
		$this->oResult->data_seek(0);
	}
}
