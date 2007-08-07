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
	Extends SimpleXMLIterator to allow the setting and getting of properties.
	Properties must be accessed using the function property since -> is already used by SimpleXMLIterator (and its parent class SimpleXMLElement).
*/

class weeSimpleXMLHack extends SimpleXMLIterator
{
	/**
		Returns the uniqid string for this object.

		@return The uniqid string.
	*/

	protected function hack()
	{
		if (empty($this['weeuniqidhack']))
			$this['weeuniqidhack'] = uniqid();
		return (string)$this['weeuniqidhack'];
	}

	/**
		Sets or get a property of a SimpleXMLIterator object.

		@param	$sName	The name of the property.
		@param	$mParam The variable to attach to this element. If null it is not attached.
		@return	mixed	The variable attached with the specified name.
	*/

	public function property($sName, $mParam = null)
	{
		static $aProperties = null;

		if (!is_null($mParam))
			$aProperties[$this->hack()][$sName] = $mParam;

		fire(!array_key_exists($sName, $aProperties[$this->hack()]), 'IllegalStateException');
		return $aProperties[$this->hack()][$sName];
	}
}

?>
