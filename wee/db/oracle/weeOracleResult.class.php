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
	Class for Oracle query results handling.
	An object of this class is created by the weeOracleDatabase's query method for SELECT statements.
*/

class weeOracleResult extends weeDatabaseResult
{
	/**
		Number of results.
	*/

	protected $iCount;

	/**
		All results.
	*/

	protected $aResults;

	/**
		Index number of the row to fetch for Iterator.
	*/

	protected $iCurrentIndex;

	/**
		Initialize the class with the result of the query.

		@param $rResult The resource for the query result returned by weeDatabase's query method.
	*/

	public function __construct($rResult)
	{
		fire(!is_resource($rResult), 'InvalidArgumentException', '$rResult must be a resource.');

		$this->iCount = @oci_fetch_all($rResult, $this->aResults, 0, -1, OCI_ASSOC + OCI_FETCHSTATEMENT_BY_ROW);
		oci_free_statement($rResult);
	}

	/**
		Return the number of results returned by the query.

		@return int The number of results.
	*/

	public function count()
	{
		return $this->iCount;
	}

	/**
		Return the current row.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function current()
	{
		if (empty($this->sRowClass))
			$a = $this->aResults[$this->iCurrentIndex];
		else
			$a = new $this->sRowClass($this->aResults[$this->iCurrentIndex]);

		return $this->processRow($a);
	}

	/**
		Fetch the next row.

		Usually used to fetch the result of a query with only one result returned,
		because if there's no result it throws an exception.

		The return value type can differ depending on the row class.
		The row class can be changed using the rowClass method.

		@return array Usually an array, or a child of weeDatabaseRow.
	*/

	public function fetch()
	{
		$this->count() == 1
			or burn('DatabaseException',
				_WT('The result set does not contain exactly one row.'));

		fire(empty($this->aResults[0]), 'DatabaseException',
			'Failed to retrieve the row because no row were returned by the query.');

		if (empty($this->sRowClass))
			$a = $this->aResults[0];
		else
			$a = new $this->sRowClass($this->aResults[0]);

		return $this->processRow($a);
	}

	/**
		Fetch all the rows returned by the query.

		The return value type can differ depending on the row class.
		The row class can be changed using the rowClass method.

		@return array Usually an array, or a child of weeDatabaseRow.
	*/

	public function fetchAll()
	{
		//TODO:handle the row class here too, and don't fire
		fire(!empty($this->sRowClass), 'IllegalStateException',
			'You cannot use a row class with weeOracleResult::fetchAll yet.');

		return $this->aResults;
	}

	/**
		Return the key of the current row.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function key()
	{
		return $this->iCurrentIndex;
	}

	/**
		Move forward to next row.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function next()
	{
		$this->iCurrentIndex++;
	}

	/**
		Rewind the Iterator to the first row.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function rewind()
	{
		$this->iCurrentIndex = 0;
	}

	/**
		Check if there is a current row after calls to rewind() or next().

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid()
	{
		return !empty($this->aResults[$this->iCurrentIndex]);
	}
}
