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
	Class for PostgreSQL query results handling.
	An object of this class is created by the weePgSQLDatabase's query method for SELECT statements.
*/

class weePgSQLResult extends weeDatabaseResult
{
	/**
		The pgsql result set.
	*/

	protected $rResult;

	/**
		Initialises a new pgsql result set.

		@param	$rResult					The pgsql result resource.
		@throw	InvalidArgumentException	$rResult is not a valid pgsql result resource.
	*/

	public function __construct($rResult)
	{
		@get_resource_type($rResult) == 'pgsql result'
			or burn('InvalidArgumentException',
				_WT('$rResult is not a valid pgsql result resource.'));

		$this->rResult = $rResult;
	}

	/**
		Return the number of results returned by the query.

		@return	int		The number of results.
	*/

	public function count()
	{
		$i = pg_num_rows($this->rResult);
		fire($i == -1, 'DatabaseException',
			'An error occurred while trying to count the number of rows returned by the query.');

		return $i;
	}

	/**
		Fetches the data of the next row of the result set.

		@return	mixed	An array containing the data of the next row or false if there is no current row.
	*/

	protected function doFetch()
	{
		return pg_fetch_assoc($this->rResult);
	}

	/**
		Rewinds the result set to its first row.
	*/

	protected function doRewind()
	{
		pg_result_seek($this->rResult, 0);
	}

	/**
		Fetches all the rows of the result set.

		The return value type can differ depending on the row class.
		The row class can be changed using the rowClass method.

		This method should not be used when iterating over the rows of the result set
		through the Iterator interface.

		@return	array(mixed)	An array of arrays or instances of weeDatabaseRow.
	*/

	public function fetchAll()
	{
		if ($this->sRowClass !== null)
			return parent::fetchAll();

		$m = pg_fetch_all($this->rResult);

		if ($m)
		{
			if ($this->bMustEncodeData)
				return weeOutput::encodeArray($m);
			return $m;
		}

		return array();
	}
}
