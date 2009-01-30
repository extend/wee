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
		if ($this->aAttributes == false)
			throw new LDAPException(_WT('weeLDAPEntry::getAttributes failed to get the attributes of the current entry.') . "\n" . ldap_error(), ldap_errno());

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
		Split the DN of the entry.

		@param $iOnlyValue Whether the RDNs are returned with only value or with their attributes. Set it to 0 to have a result like dc=example, set it to 1 to have only example.
		@return array The DN elements splitted.
		@throw InvalidArgumentException The $iOnlyValue must be set to 0 or 1.
	*/

	public function getExplodedDN($iOnlyValue)
	{
		if ($iOnlyValue != 0 && $iOnlyValue != 1) burn('InvalidArgumentException', 
			_WT('iOnlyValue must be set to 0 to get RDNs with the attributes, to get only values set it to 1.'));

		$aExplodedDN = ldap_explode_dn($this->getDN(), $iOnlyValue);

		return $aExplodedDN;
	}

	/**
		Get the DN of the entry.

		@return string The DN of the current entry.
		@throw LDAPException If an error occurs.
	*/

	public function getDN()
	{
		$sDN = ldap_get_dn($this->rLink, $this->rEntry);
		if ($sDN == false)
			throw new LDAPException(_WT('weeLDAPEntry::getDN can not find the DN for the specified entry.') . "\n" . ldap_error(), ldap_errno());

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
		if ($this->sCurrAttribute === false)
			return false;

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
		return empty($this->aAttributes[$offset]) ? false : true;
	}

	/**
		Return attribute at given offset.

		@param $offset Offset index.
		@return bool Attribute at given offset
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetGet($offset)
	{
		if (!$this->offsetExists($offset))
			return false;

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
			'The value parameter must be an array.');

		foreach ($value as $iIndex => $sValue)
			is_int($iIndex) or burn('LDAPException', 'The array of values is not valid. Indexes must be 0, 1...');

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

	public function save()
	{
		$b = ldap_mod_replace($this->rLink, $this->getDN(), $this->aAttributes);
		if ($b == false)
			throw new LDAPException(_WT('weeLDAPEntry::save can not save the attributes for the current DN".') . "\n" . ldap_error(), ldap_errno());
	}

	/**
		Check if there is a current attribute after calls to rewind() or next().

		@return bool Whether there is a current attribute after calls to rewind() or next();
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid() 
	{
		return $this->aAttributes[$this->iCurrentIndex] !== false;
	}
}
