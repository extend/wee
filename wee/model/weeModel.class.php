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
	Base class for defining a model.
*/

abstract class weeModel extends weeDataSource implements ArrayAccess
{
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
		fire(!array_key_exists($offset, $this->aData), 'InvalidArgumentException',
			'The value for offset ' . $offset . ' was not found in the data.');

		if ($this->bMustEncodeData)
			return weeOutput::encodeValue($this->aData[$offset]);
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
		Copy data directly from an array.

		@param $aData Array containing the data to copy from.
		@return $this
	*/

	public function setFromArray($aData)
	{
		fire(!is_array($aData), 'InvalidArgumentException', '$aData must be an array.');
		$this->aData = $aData + $this->aData;

		return $this;
	}
}
