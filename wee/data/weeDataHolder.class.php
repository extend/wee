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
	Base class for data holders.

	Values can be accessed like an array.
*/

class weeDataHolder extends weeDataSource implements ArrayAccess, Mappable
{
	/**
		The data.
	*/

	protected $aData = array();

	/**
		Initialize the data holder.

		@param $aData The data to hold.
	*/

	public function __construct(array $aData = array())
	{
		$this->aData = $aData;
	}

	/**
		Return whether the given key exist in the data.

		@param	$sKey	The key.
		@return	bool	Whether the key exists.
	*/

	public function offsetExists($sKey)
	{
		return array_key_exists($sKey, $this->aData);
	}

	/**
		Return the value at given key.

		@param	$sKey	The key.
		@return	mixed	The value.
		@throw	InvalidArgumentException The key does not exist.
	*/

	public function offsetGet($sKey)
	{
		$this->offsetExists($sKey) or burn('InvalidArgumentException',
			sprintf(_WT('Key "%s" does not exist.'), $sKey));

		if ($this->getEncoder() !== null)
			return $this->getEncoder()->encode($this->aData[$sKey]);
		return $this->aData[$sKey];
	}

	/**
		Set value at a given key.

		@param $sKey	The key.
		@param $mValue	The value.
	*/

	public function offsetSet($sKey, $mValue)
	{
		$this->aData[$sKey] = $mValue;
	}

	/**
		Unset value at a given key.

		@param $sKey The key.
	*/

	public function offsetUnset($sKey)
	{
		unset($this->aData[$sKey]);
	}

	/**
		Add a value to the data.

		The values may be given as an array or a mappable or traversable object.

		@param	$mValues The values to set.
		@return	$this
		@throw	InvalidArgumentException The given argument is not valid.
	*/

	public function setFromArray($mValues)
	{
		if (is_object($mValues)) {
			if ($mValues instanceof Mappable)
				$mValues = $mValues->toArray();
			elseif ($mValues instanceof Traversable)
				$mValues = iterator_to_array($mValues);

			throw new InvalidArgumentException(
				_WT('$mValues is an object but cannot be transformed as an array.'));
		}
		else
			is_array($mValues) or burn('InvalidArgumentException',
				_WT('$mValues must be an array or a mappable or traversable object.'));

		$this->aData = $mValues + $this->aData;
		return $this;
	}

	/**
		Return data as an array.

		@return array The data as an array.
	*/

	public function toArray()
	{
		if ($this->getEncoder() !== null)
			return $this->encodeArray($this->aData);
		return $this->aData;
	}
}
