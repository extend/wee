<?php

/*
	Web:Extend
	Copyright (c) 2008 Dev:Extend

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
	Wrapper around real weeDatabaseResult objects used to bypass calls made to them
	and keep the EXPLAIN results available for later use.

	@warning Experimental.
*/

class weeExplainSQLResult extends weeDatabaseResult implements ArrayAccess
{
	/**
		Real results object returned by the query.
	*/

	protected $oResult;

	/**
		Initialize the class with the result of the query.

		@param $oResult The objet created after executing the query.
	*/

	public function __construct($oResult)
	{
		$oResult instanceof weeDatabaseResult or burn('InvalidArgumentException', '$oResult must be an instance of weeDatabaseResult.');
		$this->oResult = $oResult;
	}

	/**
		Return the number of results returned by the query.
		A real number is returned, the number of rows returned by the EXPLAIN query.

		@return int The number of results.
	*/

	public function count()
	{
		return count($this->oResult);
	}

	/**
		Return the current row.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function current()
	{
		return $this->oResult->current();
	}

	/**
		This method is bypassed and only $this is returned.

		@return $this
	*/

	public function fetch()
	{
		return $this;
	}

	/**
		This method is bypassed and only $this is returned.

		return $this
	*/

	public function fetchAll()
	{
		return $this;
	}

	/**
		Return the key of the current row.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function key()
	{
		return $this->oResult->key();
	}

	/**
		Move forward to next row.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function next()
	{
		$this->oResult->next();
	}

	/**
		Bypassed, always returns true.

		@param	$offset	Offset name.
		@return	bool	Always true.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetExists($offset)
	{
		return true;
	}

	/**
		Bypassed, return $this.

		@param	$offset	Offset name.
		@return	$this
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetGet($offset)
	{
		return $this;
	}

	/**
		Bypassed, do nothing.

		@param	$offset	Offset name.
		@param	$value	New value for this offset.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetSet($offset, $value)
	{
	}

	/**
		Bypassed, do nothing.

		@param	$offset	Offset name.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetUnset($offset)
	{
	}

	/**
		Rewind the Iterator to the first row.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function rewind()
	{
		$this->oResult->rewind();
	}

	/**
		Check if there is a current row after calls to rewind() or next().

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid()
	{
		return $this->oResult->valid();
	}
}
