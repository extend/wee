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
	Class for accessing LDAP-based directories. 
*/

class weeLDAP
{
	/**
		LDAP link identifier. 
	*/

	protected $rLink;

	/**
		Establishes a simple connection to an LDAP server on a specified hostname and port, and binds to the LDAP directory with specified RDN and password.
		For binding anonymously, you don't need to specify RDN and password.

		Parameters:
			host: The LDAP server.
			port: The port to connect.
			rdn: The Relative Distinguished Name.
			password: The password to use.

		@param $aPrams List of parameters used to initalize the connection and authentication.
		@throw ConfigurationException LDAP support must be enabled.
		@throw InvalidArgumentException The host parameter must be specified.
		@throw LDAPException If an error occurs.
	*/

	public function __construct($aParams = array())
	{
		function_exists('ldap_connect') or burn('ConfigurationException', 'LDAP support is missing.');

		empty($aParams['host']) and burn('InvalidArgumentException', 'The host parameter must not be empty.');

		$this->rLink = ldap_connect(array_value($aParams,'host'), array_value($aParams, 'port', 389));
		if ($this->rLink === false)
			throw new LDAPException(
				sprintf(_WT('Can not connect to "%s" :'), array_value($aParams,'host')) . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);

		$b = ldap_bind($this->rLink, array_value($aParams, 'rdn', null), array_value($aParams, 'password', null));
		if ($b === false)
			throw new LDAPException(
				sprintf(_WT('Can not bind the RDN "%s".'), array_value($aParams,'rdn'), null) . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);
	}

	/**
		Add an entry to a specific DN.

		@param $sDN The Distinguished Name.
		@param $aEntry Entry attributes for the specified DN.
		@throw InvalidArgumentException $aEntry must be an array.
		@throw LDAPException If an error occurs.
	*/

	public function insert($sDN, $aEntry)
	{
		is_array($aEntry) or burn('InvalidArgumentException', 'The aEntry parameter must be an array.');

		$b = ldap_add($this->rLink, $sDN, $aEntry); 
		if ($b === false) 
			throw new LDAPException(
				_WT('Can not add entry to the specified DN "%s".') . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);
	}

	/**
		Compares the value of the attribute of an entry for a specific DN.

		@param $sDN The Distinguished Name of an LDAP entity. 
		@param $sAttribute The attribute name.
		@param $sValue The compared value.
		@return bool Whether the value matched.
		@throw LDAPException If an error occurs.
	*/

	public function isEqual($sDN, $sAttribute, $sValue)
	{
		$b = ldap_compare($this->rLink, $sDN, $sAttribute, $sValue);
		if ($b === -1)
			throw new LDAPException(
				_WT('Failed to compare the value of the attribute.') . "\n" . ldap_error($this->rLink), 
				ldap_errno($this->rLink)
			);

		return $b;
	}

	/**
		Deletes an entry in the LDAP directory. 

		@param $sDN The Distinguished Name of an LDAP entity. 
		@throw LDAPException If an error occurs.
	*/

	public function delete($sDN)
	{
		$b = ldap_delete($this->rLink, $sDN);
		if ($b === false)
			throw new LDAPException(
				_WT('Failed to delete the DN.') . "\n" . ldap_error($this->rLink), 
				ldap_errno($this->rLink)
			);
	}

	/**
		Modify the existing entries in the LDAP directory.

		@param $sDN The Distinguished Name of an LDAP entity.
		@throw LDAPException If an error occurs.
	*/

	public function modify($sDN, $aEntry)
	{
		$b = ldap_modify($this->rLink, $sDN, $aEntry);
		if ($b === false)
			throw new LDAPException(
				_WT('Failed to modify the entry.') . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);
	}

	/**
		Read an entry.

		@param $sDN The Distinguished Dame of an LDAP entity.
		@param $sFilter The filter for the read by default objectClass=*.
		@return weeLDAPResult The object containing the result.
	*/

	public function fetch($sDN, $sFilter = 'objectClass=*')
	{
		$r = ldap_read($this->rLink, $sDN, $sFilter);
		if ($r === false)
			throw new LDAPException(
				_WT('Failed to read in the specified DN.') . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);

		return new weeLDAPResult($this->rLink, $r);
	}

	/**
		Perform the search for a specified filter in the directory.

		@param $sDN The Distinguished Name of an LDAP entity.
		@param $sFilter The filter for the search.
		@param $bRecursive Whether to include subdirectories.
		@return weeLDAPResult The object containing the search result.
		@throw LDAPException If an error occurs.
	*/

	public function search($sDN, $sFilter, $bRecursive = true)
	{
		if ($bRecursive)
			$r = ldap_search($this->rLink, $sDN, $sFilter);
		else
			$r = ldap_list($this->rLink, $sDN, $sFilter);

		if ($r === false)
			throw new LDAPException(
				_WT('Failed to search in the specified DN.') . "\n" . ldap_error($this->rLink), 
				ldap_errno($this->rLink)
			);

		return new weeLDAPResult($this->rLink, $r);
	}

	/**
		Rename the entry.

		@param $sFromDN The actual Distinguished Name.
		@param $sToDN The new Distinguished Name.
		@param $bDeleteOldRDN Whether the old DN has to be deleted.
		@throw LDAPException If an error occurs.
	*/

	public function rename($sFromDN, $sToDN, $bDeleteOldRDN = true)
	{
		$b = ldap_rename($this->rLink, $sFromDN, $sToDN, null, $bDeleteOldRDN);
		if ($b === false)
			throw new LDAPException(
				_WT('Failed to rename the entry.') . "\n" . ldap_error($this->rLink), 
				ldap_errno($this->rLink)
			);
	}
}
