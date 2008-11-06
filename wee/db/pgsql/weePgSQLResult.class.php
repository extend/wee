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
	Class for PostgreSQL query results handling.
	An object of this class is created by the weePgSQLDatabase's query method for SELECT statements.
*/

class weePgSQLResult extends weeDatabaseResult
{
	/**
		Resource for this query result.
	*/

	protected $rResult;

	/**
		Data from the current row.
	*/

	protected $aCurrentFetch;

	/**
		Index number of the row to fetch.
		Second parameter of pg_fetch_assoc.
	*/

	protected $iCurrentIndex;

	/**
		Initialize the class with the result of the query.

		@param $rResult The resource for the query result returned by weeDatabase's query method.
	*/

	public function __construct($rResult)
	{
		fire(!is_resource($rResult), 'InvalidArgumentException', '$rResult must be a resource.');
		$this->rResult = $rResult;
	}

	/**
		Return the number of results returned by the query.

		@return int The number of results.
	*/

	public function count()
	{
		$i = pg_num_rows($this->rResult);
		fire($i == -1, 'DatabaseException',
			'An error occurred while trying to count the number of rows returned by the query.');

		return $i;
	}

	/**
		Return the current row.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function current()
	{
		return $this->processRow($this->aCurrentFetch);
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

		$a = pg_fetch_assoc($this->rResult);
		fire($a === false, 'DatabaseException',
			'Failed to retrieve the row. Might be because no row were returned by the query,' .
			' or because you are incorrectly trying to loop through all the rows using this method.');

		if (!empty($this->sRowClass))
			$a = new $this->sRowClass($a);

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
			'You cannot use a row class with weePgSQLResult::fetchAll yet.');

		$m = pg_fetch_all($this->rResult);
		return $m === false ? array() : $m;
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
		$this->aCurrentFetch = @pg_fetch_assoc($this->rResult, $this->iCurrentIndex);

		if (!empty($this->sRowClass) && $this->aCurrentFetch !== false)
			$this->aCurrentFetch = new $this->sRowClass($this->aCurrentFetch);

		return ($this->aCurrentFetch !== false);
	}
}

?>
