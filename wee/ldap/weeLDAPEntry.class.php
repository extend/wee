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
	Class for managing an LDAP entry and its attributes. 
*/

class weeLDAPEntry implements ArrayAccess, Iterator
{
	/**
		LDAP link identifier. 
	*/

	protected $rLink;

	/**
		LDAP link identifier of the result. 
	*/

	protected $rResult;

	/**
		LDAP link identifier of the current entry. 
	*/

	protected $rEntry;

	/**
		Current attribute. 
	*/

	protected $sCurrAttribute;

	/**
		The attributes for the current entry. 
	*/

	protected $aAttributes;

	/**
		Key of the current iterated element. 
	*/

	protected $i;

	/**
		The Distinguished Name.
	*/

	protected $sDN;

	/**
		Initialise the weeLDAPEntry object.

		@param $rLink The connection link identifier 
		@param $rResult The search result link identifier.
		@param $rEntry The entry link identifier.
	*/

	public function __construct($rLink, $rResult, $rEntry) //TODO:remove $rResult : search result
	{
		$this->rEntry	= $rEntry;
		$this->rLink	= $rLink;
		$this->rResult	= $rResult;

		$this->rewind();
	}

	/**
		Return the the current attribute.

		@return string The current attribute.
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function current()
	{
		return $this->sCurrAttribute;
	}

	/**
		Retrieve the attributes of the current entry.
		The structure of the returned values is as follow :
			$aAttributes['count'] = Number of returned attributes.

			$aAttributes[i] = ith attribute, also $aAttributes['attribute'].
			$aAttributes[i]['count'] = Numbers of values for the ith attribute, also $aAttributes['attribute']['count'].

		@return array The attributes elements.
		@throw LDAPException If an error occurs.
	*/

	public function getAttributes()
	{
		$this->aAttributes	= ldap_get_attributes($this->rLink, $this->rEntry);
		$this->aAttributes	=== false and burn('LDAPException', _WT('weeLDAPEntry::getAttributes failed to get the attributes of the current entry.'));

		return $this->aAttributes;
	}

	/**
		Retrieve the values of the current attribute.
		The structure of the returned value is as follow :
			$aValues['count'] = Number of returned values.
			$aValues[i] = Indexed values.

		@return array The values elements.
	*/

	public function getAttributeValues()
	{
		if (!$this->current())
			return false;

		$aValues = ldap_get_values($this->rLink, $this->rEntry, $this->sCurrAttribute);

		return $aValues;
	}

	/**
		Split the DN of the current entry.
		The structure of the returned value is as follow :
			$aExplodedDN['count'] = Number of returned values.
			$aExplodedDN[i] = Indexed DN elements.

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
		Find a DN of the current entry.

		@return string The DN of the current entry.
		@throw LDAPException If an error occurs.
	*/

	public function getDN()
	{
		$this->sDN	= ldap_get_dn($this->rLink, $this->rEntry);
		$this->sDN	=== false and burn('LDAPException', _WT('weeLDAPEntry::getDN can not find the DN for the specified entry.'));

		return $this->sDN;
	}

	/**
		Return the key of the current attribute.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function key()
	{
		return $this->i;
	}

	/**
		Add one or more attributes to the specified DN.

		@param $sDN The Distinguished Name.
		@param $aEntry Entry attributes for the specified DN.
		@return bool Whether the attributes where added.
		@throw LDAPException If an error occurs.
	*/

	public function modAdd($sDN, $aEntry)
	{
		$b	= ldap_mod_add($this->rLink, $sDN, $aEntry);
		$b	=== false and burn('LDAPException', sprintf(_WT('weeLDAPEntry::modAdd can not add the attributes for the DN "%s".'), $sDN));

		return $b;
	}

	/**
		Delete one or more attributes of the specified DN.

		@param $sDN The Distinguished Name.
		@param $aEntry Entry attributes for the specified DN.
		@return bool Whether the attributes where removed.
		@throw LDAPException If an error occurs.
	*/

	public function modDelete($sDN, $aEntry)
	{
		$b	= ldap_mod_del($this->rLink, $sDN, $aEntry);
		$b	=== false and burn('LDAPException', sprintf(_WT('weeLDAPEntry::modDelete can not delete the attributes of the DN "%s"'), $sDN));

		return $b;
	}

	/**
		Modify one or more attributes of the specified DN.

		@param $sDN The Distinguished Name.
		@param $aEntry Entry attributes for the specified DN.
		@return bool Whether the attributes where modified.
		@throw LDAPException If an error occurs.
	*/

	public function modUpdate($sDN, $aEntry)
	{
		$b 	= ldap_mod_replace($this->rLink, $sDN, $aEntry);
		$b	=== false and burn('LDAPException', sprintf(_WT('weeLDAPEntry::modModify can not modify the attributes for the DN "%s"'), $sDN));

		return $b;
	}

	/**
		Move forward to next attribute, and get its values.

		@return string The next attribute.
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function next()
	{
		if ($this->i == -1)
			$this->sCurrAttribute = ldap_first_attribute($this->rLink, $this->rEntry);
		else
			$this->sCurrAttribute = ldap_next_attribute($this->rLink, $this->rEntry);

		if ($this->sCurrAttribute === false) {
			//~ $this->i = -1;?
			return false;
		}

		$this->i++;
		$this->getAttributeValues();
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
			'The parameter must be an array.');

		foreach($value as $i => $v)
			$this->aAttributes[$offset][$i] = $v;

		return true;
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
		$this->i = -1;
		$this->next();
	}

	/**
		Save the attribute and its values to the current state.

		@throw LDAPException If an error occurs.
	*/

	public function save()
	{
		//~ Clean the array.
		foreach($this->aAttributes as $k => $v){
			if (is_string($k)) {
				$tmp[$k] = $v;
				unset($tmp[$k]['count']);
			}
		}

		unset($tmp['count']);

		$b	= ldap_mod_replace($this->rLink, $this->getDN(), $tmp);
		$b	=== false and burn('LDAPException', sprintf(_WT('weeLDAPEntry::save can not save the attributes for the DN "%s".'), $sDN));

		//~ Update the attributes elements of the entry after the replacement on the server.
		$this->getAttributes();
	}

	/**
		Check if there is a current attribute after calls to rewind() or next().

		@return bool Whether there is a current attribute after calls to rewind() or next();
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid() 
	{
		return $this->sCurrAttribute !== false;
	}
}
