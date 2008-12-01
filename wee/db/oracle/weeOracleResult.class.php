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
	Class for Oracle query results handling.
	An object of this class is created by the weeOracleDatabase's query method for SELECT statements.
*/

class weeOracleResult extends weeDatabaseResult
{
	/**
		The number of rows in the result set.
	*/

	protected $iCount;

	/**
		The rows of the result set.
	*/

	protected $aRows;

	/**
		Initialises a new oracle result set.

		@param	$rResult					The oracle result resource.
		@throw	InvalidArgumentException	$rResult is not a valid oci8 statement resource.
	*/

	public function __construct($rResult)
	{
		@get_resource_type($rResult) == 'oci8 statement'
			or burn('InvalidArgumentException',
				_WT('$rResult is not a valid oci8 statement resource.'));

		$this->iCount = @oci_fetch_all($rResult, $this->aRows, 0, -1, OCI_ASSOC + OCI_FETCHSTATEMENT_BY_ROW);
		oci_free_statement($rResult);
	}

	/**
		Return the number of results returned by the query.

		@return	int		The number of results.
	*/

	public function count()
	{
		return $this->iCount;
	}

	/**
		Fetches the data of the next row of the result set.

		@return	mixed	An array containing the data of the next row or false if there is no current row.
	*/

	protected function doFetch()
	{
		$m = current($this->aRows);
		next($this->aRows);
		return $m;
	}

	/**
		Rewinds the result set to its first row.
	*/

	protected function doRewind()
	{
		reset($this->aRows);
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

		if ($this->bMustEncodeData)
			return weeOutput::encodeArray($this->aRows);
		return $this->aRows;
	}
}
