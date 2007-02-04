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
	Configuration data wrapper.
*/

class weeConfig implements ArrayAccess
{
	/**
		Contains the configuration data.
	*/

	protected $aConfig = array();

	/**
		Check if the $offset offset does exist.

		@param	$offset	The offset checked.
		@return	bool	True if it exists.
	*/

	public function offsetExists($offset)
	{
		return isset($this->aConfig[$offset]);
	}

	/**
		Returns the $offset offset value, or null if it does not exist.

		@param	$offset	The offset to return.
		@return	mixed	The value of the offset.
	*/

	public function offsetGet($offset)
	{
		if (!array_key_exists($offset, $this->aConfig))
			return null;
		return $this->aConfig[$offset];
	}

	/**
		Set the value of $offset.

		@param	$offset	The offset to set.
		@param	$value	The new value of the offset.
	*/

	public function offsetSet($offset, $value)
	{
		$this->aConfig[$offset] = $value;
	}

	/**
		Unset the $offset offset.

		@param	$offset	The offset to unset.
	*/

	public function offsetUnset($offset)
	{
		unset($this->aConfig[$offset]);
	}
}

?>
