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
	Class for MSSQL query results handling.
	An object of this class is created by the weeMSSQLDatabase's query method for SELECT statements.
*/

class weeMSSQLResult extends weeDatabaseResult
{
	/**
		The mssql result set.
	*/

	protected $rResult;

	/**
		Initialises a new mssql result set.

		@param	$rResult					The mssql result resource.
		@throw	InvalidArgumentException	The resource is not a valid mssql result.
	*/

	public function __construct($rResult)
	{
		is_resource($rResult) && get_resource_type($rResult) == 'mssql result' or burn('InvalidArgumentException',
			sprintf(_WT('The given variable must be a resource of type "%s".'), 'mssql result'));

		$this->rResult = $rResult;
	}

	/**
		Return the number of results returned by the query.

		@return	int		The number of results.
	*/

	public function count()
	{
		return mssql_num_rows($this->rResult);
	}

	/**
		Fetches the data of the next row of the result set.

		@return	mixed	An array containing the data of the next row or false if there is no current row.
	*/

	protected function doFetch()
	{
		return mssql_fetch_assoc($this->rResult);
	}

	/**
		Rewinds the result set to its first row.
	*/

	protected function doRewind()
	{
		// mssql_data_seek triggers a warning when the result set is empty.
		@mssql_data_seek($this->rResult, 0);
	}
}
