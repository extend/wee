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
	Class for MySQL query results handling.

	Instances of this class are returned by weeMySQLDatabase's query method and
	should not be instantiated manually.
*/

class weeMySQLResult extends weeDatabaseResult
{
	/**
		The mysql result set.
	*/

	protected $rResult;

	/**
		Initialises a new mysql result set.

		@param	$rResult					The mysql result resource.
		@throw	InvalidArgumentException	The resource is not a valid mysql result.
	*/

	public function __construct($rResult)
	{
		@get_resource_type($rResult) == 'mysql result' or burn('InvalidArgumentException',
			sprintf(_WT('The given variable must be a resource of type "%s".'), 'mysql result'));

		$this->rResult = $rResult;
	}

	/**
		Return the number of results returned by the query.

		@return	int		The number of results.
	*/

	public function count()
	{
		$i = mysql_num_rows($this->rResult);
		$i === false and burn('DatabaseException',
			_WT('An error occurred while trying to count the number of rows in the result set.'));

		return $i;
	}

	/**
		Fetches the data of the next row of the result set.

		@return	mixed	An array containing the data of the next row or false if there is no current row.
	*/

	protected function doFetch()
	{
		return mysql_fetch_assoc($this->rResult);
	}

	/**
		Rewinds the result set to its first row.
	*/

	protected function doRewind()
	{
		@mysql_data_seek($this->rResult, 0);
	}
}
