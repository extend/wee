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

	public function __construct(weeDatabaseResult $oResult)
	{
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
		Fetches the data of the next row of the result set.

		@return	mixed	An array containing the data of the next row or false if there is no current row.
	*/

	protected function doFetch()
	{
		$m = $this->oResult->current();
		$this->oResult->next();
		return $m;
	}

	/**
		Rewinds the result set to its first row.
	*/

	protected function doRewind()
	{
		$this->oResult->rewind();
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
}
