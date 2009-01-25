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
	Base class for defining a model.
*/

abstract class weeModel extends weeDataSource implements ArrayAccess, Iterator, Mappable
{
	/**
		Key and value for the currently iterated element.
	*/

	protected $aCurrentElement;

	/**
		Data for the instances of this model.
	*/

	protected $aData = array();

	/**
		Creates a new instance of this model with the data passed as parameter.

		@param $aData Data to be set at initialization.
	*/

	public function __construct($aData = array())
	{
		$this->setFromArray($aData);
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
		return array_key_exists($offset, $this->aData);
	}

	/**
		Returns value at given offset.

		@param	$offset	Offset name.
		@return	bool	value at given offset
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetGet($offset)
	{
		array_key_exists($offset, $this->aData) or burn('InvalidArgumentException',
			sprintf(_WT('The value for offset "%s" was not found in the data.'), $offset));

		if ($this->bMustEncodeData)
			return weeOutput::instance()->encode($this->aData[$offset]);
		return $this->aData[$offset];
	}

	/**
		Sets a new value for the given offset.

		@param	$offset	Offset name.
		@param	$value	New value for this offset.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetSet($offset, $value)
	{
		$this->aData[$offset] = $value;
	}

	/**
		Unsets offset.

		@param	$offset	Offset name.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetUnset($offset)
	{
		unset($this->aData[$offset]);
	}

	/**
		Rewind the Iterator to the first element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function rewind()
	{
		reset($this->aData);
	}

	/**
		Copy data directly from an array.

		@param $aData Array containing the data to copy from.
		@return $this
	*/

	public function setFromArray($aData)
	{
		is_array($aData) or burn('InvalidArgumentException', _WT('$aData must be an array.'));
		$this->aData = $aData + $this->aData;

		return $this;
	}

	/**
		Returns the data as array, since we can't cast weeModel to retrieve the array's data.

		@return array Object's data.
	*/

	public function toArray()
	{
		return $this->aData;
	}

	/**
		Check if there is a current element after calls to rewind() or next().

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid()
	{
		$this->aCurrentElement = each($this->aData);
		return $this->aCurrentElement !== false;
	}
}
