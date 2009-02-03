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
	Class for managing attributes of an LDAP entry.
*/

class weeLDAPEntry implements ArrayAccess, Iterator
{
	/**
		LDAP connection link identifier.
	*/

	protected $rLink;

	/**
		LDAP link identifier of the entry.
	*/

	protected $rEntry;

	/**
		The attributes for the current entry.
	*/

	protected $aAttributes;

	/**
		Key of the current iterated element.
	*/

	protected $iCurrentIndex;

	/**
		Initialise the weeLDAPEntry object.

		@param $rLink The connection link identifier 
		@param $rEntry The entry link identifier.
	*/

	public function __construct($rLink, $rEntry)
	{
		$this->rEntry	= $rEntry;
		$this->rLink	= $rLink;

		$this->aAttributes = ldap_get_attributes($this->rLink, $this->rEntry);
		if ($this->aAttributes === false)
			throw new LDAPException(
				_WT('Failed to get the attributes of the current entry.') . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);

		// Clean the array $aAttributes.
		// Remove the "count" elements and attribute indexes, for avoiding LDAPException in weeLDAPEntry::save.
		// The array will be like: array('attribute1' => array(value1, value2), 'attribute2' => ...) instead of the array returned by ldap_get_attributes.

		foreach ($this->aAttributes as $mAttrKey => $mAttrValue) {
			if (is_string($mAttrKey)) {
				$this->aAttributes[$mAttrKey] = $mAttrValue;
				unset($this->aAttributes[$mAttrKey]['count']);
			} else
				unset($this->aAttributes[$mAttrKey]);
		}

		unset($this->aAttributes['count']);
	}

	/**
		Return the values of the current attribute.

		@return array The attribute values.
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function current()
	{
		return $this->aAttributes[$this->iCurrentIndex];
	}

	/**
		Get the DN of the entry.

		@return string The DN of the current entry.
		@throw LDAPException If an error occurs.
	*/

	public function getDN()
	{
		$sDN = ldap_get_dn($this->rLink, $this->rEntry);
		if ($sDN === false)
			throw new LDAPException(
				_WT('Can not get the DN for the current entry.') . "\n" . ldap_error($this->rLink), 
				ldap_errno($this->rLink)
			);

		return $sDN;
	}

	/**
		Return the key of the current attribute.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function key()
	{
		return $this->iCurrentIndex;
	}

	/**
		Move forward to next attribute.

		@return string The next attribute.
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function next()
	{
		$this->iCurrentIndex++;
	}

	/**
		Return whether offset exists.

		@param $offset Offset index.
		@return bool Whether the offset exists.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetExists($offset)
	{
		return !empty($this->aAttributes[$offset]);
	}

	/**
		Return attribute at given offset.

		@param $offset Offset index.
		@return mixed Attribute at given offset
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetGet($offset)
	{
		return $this->aAttributes[$offset];
	}

	/**
		Set a new value for the given offset.

		@param $offset Offset name.
		@param $value New value for this offset.
		@throw InvalidArgumentException	The value must be an array.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetSet($offset, $value)
	{
		is_array($value) or burn('InvalidArgumentException',
			_WT('The value parameter must be an array.'));

		foreach ($value as $iIndex => $sValue)
			is_int($iIndex) or burn('LDAPException', _WT('The array of values is not valid. Indexes must be 0, 1...'));

		//TODO:consecutive indexes

		$this->aAttributes[$offset] = $value;
	}

	/**
		Unset offset.

		@param $offset Offset name.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetUnset($offset)
	{
		unset($this->aAttributes[$offset]);
	}

	/**
		Rewind the Iterator to the first attribute.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function rewind()
	{
		$this->iCurrentIndex = 0;
	}

	/**
		Save the attributes and their values to the server for the current entry.

		@throw LDAPException If an error occurs.
	*/

	public function update()
	{
		$b = ldap_mod_replace($this->rLink, $this->getDN(), $this->aAttributes);
		if ($b === false)
			throw new LDAPException(
				_WT('Can not save the attributes for the current DN.') . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);
	}

	/**
		Check if there is a current attribute after calls to rewind() or next().

		@return bool Whether there is a current attribute after calls to rewind() or next();
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid() 
	{
		return !empty($this->aAttributes[$this->iCurrentIndex]);
	}
}
