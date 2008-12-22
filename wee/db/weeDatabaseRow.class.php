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
	Base class for database result items.
	Values can be accessed like an array.
*/

abstract class weeDatabaseRow extends weeDataSource implements ArrayAccess, Iterator
{
	/**
		Key of the current iterated element.
	*/

	protected $aCurrentElement;

	/**
		The values returned by the database for this row.
	*/

	protected $aRow;

	/**
		Initialize the row data.

		@param $aRow The row data.
	*/

	public function __construct($aRow)
	{
		empty($aRow) || !is_array($aRow) and burn('InvalidArgumentException', '$aRow must be a non-empty array.');
		$this->aRow = $aRow;
	}

	/**
		Return the current element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function current()
	{
		return $this->aCurrentElement['value'];
	}

	/**
		Return the key of the current element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function key()
	{
		return $this->aCurrentElement['key'];
	}
	/**
		Move forward to next element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function next()
	{
	}

	/**
		Returns whether offset exists.

		@param	$offset	Offset name.
		@return	bool	Whether the offset exists.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->aRow);
	}

	/**
		Returns value at given offset.

		@param	$offset	Offset name.
		@return	bool	value at given offset
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetGet($offset)
	{
		array_key_exists($offset, $this->aRow) or burn('InvalidArgumentException',
			'The value for offset ' . $offset . ' was not found in the data.');

		if ($this->bMustEncodeData)
			return weeOutput::encodeValue($this->aRow[$offset]);
		return $this->aRow[$offset];
	}

	/**
		Sets a new value for the given offset.

		@param	$offset	Offset name.
		@param	$value	New value for this offset.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetSet($offset, $value)
	{
		$this->aRow[$offset] = $value;
	}

	/**
		Unsets offset.

		@param	$offset	Offset name.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetUnset($offset)
	{
		unset($this->aRow[$offset]);
	}

	/**
		Rewind the Iterator to the first element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function rewind()
	{
		reset($this->aRow);
	}

	/**
		Check if there is a current element after calls to rewind() or next().

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid()
	{
		$this->aCurrentElement = each($this->aRow);
		return $this->aCurrentElement !== false;
	}
}
