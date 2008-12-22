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
	Base class for database query results handling.

	Instances of this class are returned by weeDatabase's query method and
	should not be instantiated manually.
*/

abstract class weeDatabaseResult extends weeDataSource implements Countable, Iterator
{
	/**
		The class used to return row's data.
		If empty, an array will be returned.
	*/

	protected $sRowClass;

	/**
		The current fetched row.
	*/

	protected $mCurrentFetch;

	/**
		The index of the current fetched row.
	*/

	protected $iCurrentIndex;

	/**
		Database result sets cannot be cloned.
	*/

	private final function __clone()
	{
	}

	/**
		Returns the current row.

		@return	mixed	Either an array or an instance of weeDatabaseRow or false if there is no current row.
		@see			http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function current()
	{
		if ($this->mCurrentFetch === null)
			$this->mCurrentFetch = $this->doFetch();

		if ($this->mCurrentFetch)
			return $this->processRow($this->mCurrentFetch);
		return false;
	}

	/**
		Fetches the data of the next row of the result set.

		@return	mixed	An array containing the data of the next row or false if there is no current row.
	*/

	abstract protected function doFetch();

	/**
		Rewinds the result set to its first row.
	*/

	abstract protected function doRewind();

	/**
		Fetches the next row.

		Used to fetch the only row of the result set.
		If the result set is empty or contain more than one row.

		The return value type can differ depending on the row class.
		The row class can be changed using the rowClass method.

		@return	mixed				An array or an instance of weeDatabaseRow.
		@throw	DatabaseException	The result set does not contain exactly one row.
	*/

	public function fetch()
	{
		$this->count() == 1
			or burn('DatabaseException',
				_WT('The result set does not contain exactly one row.'));

		$this->rewind();
		return $this->current();
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
		return iterator_to_array($this);
	}

	/**
		Returns the key of the current row.

		@return	mixed	The key of the current row or false if there is no current row.
		@see			http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function key()
	{
		return $this->iCurrentIndex;
	}

	/**
		Move forward to next row.

		@see	http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function next()
	{
		$this->mCurrentFetch = $this->doFetch();
		$this->iCurrentIndex++;
	}

	/**
		Rewinds the Iterator to the first row.

		@see	http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function rewind()
	{
		$this->mCurrentFetch = null;
		$this->iCurrentIndex = 0;
		$this->doRewind();
	}

	/**
		Encodes the row if needed.

		@param	$aRow	The data row.
		@return	array	The data row encoded, if applicable.
	*/

	protected function processRow($aRow)
	{
		if ($this->sRowClass !== null)
			$aRow = new $this->sRowClass($aRow);

		if ($this->bMustEncodeData)
		{
			if ($aRow instanceof weeDataSource)
				return $aRow->encodeData();

			return weeOutput::encodeArray($aRow);
		}

		return $aRow;
	}

	/**
		Changes the type of the return for fetch and fetchAll methods and the
		Iterator interface.

		By default they return an array containing the row values,
		but a child class of weeDatabaseRow can be specified that will be used
		to create objects containing the row values.

		This can be used after a query if you want to abstract your result in
		an object and add methods for easy manipulation of this result.

		@param	$sClass	The class used to return row's data.
		@return	$this	Used to chain methods.
	*/

	public function rowClass($sClass)
	{
		empty($sClass) and burn('InvalidArgumentException', '$sClass must not be empty.');

		$this->sRowClass = $sClass;
		return $this;
	}

	/**
		Returns whether there is a current row after calls to rewind() or next().

		@see	http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid()
	{
		if ($this->mCurrentFetch === null)
			$this->mCurrentFetch = $this->doFetch();

		return $this->mCurrentFetch !== false;
	}
}
