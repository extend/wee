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

class weeDatabaseRow implements ArrayAccess
{
	protected $bEncodeResults = false;
	protected $aRow;

	public function __construct($aRow)
	{
		fire(empty($aRow) || !is_array($aRow), 'InvalidArgumentException');
		$this->aRow = $aRow;
	}

	public function encodeResults()
	{
		$this->bEncodeResults = true;
		return $this;
	}

	public function offsetExists($offset)
	{
		return isset($this->aRow[$offset]);
	}

	public function offsetGet($offset)
	{
		fire(!array_key_exists($offset, $this->aRow), 'InvalidArgumentException');

		if ($this->bEncodeResults)
			return weeOutput::encodeValue($this->aRow[$offset]);
		return $this->aRow[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->aRow[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->aRow[$offset]);
	}
}

?>
