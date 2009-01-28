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

		empty($aParams['host']) and burn('InvalidArgumentException', 'The `host` parameter must not be empty.');

		$this->rLink = ldap_connect(array_value($aParams,'host'), array_value($aParams, 'port', 389));
		$this->rLink === false and burn('LDAPException', sprintf(_WT('Can not connect to "%s".'), array_value($aParams,'host')));

		$b = ldap_bind($this->rLink, array_value($aParams, 'rdn', null), array_value($aParams, 'password', null));
		$b === false and burn('LDAPException', sprintf(_WT('Can not bind the RDN "%s".'), array_value($aParams, 'rdn', null)));

	}

	/**
		Unbind from LDAP directory.
	*/

	public function __destruct()
	{
		ldap_unbind($this->rLink);
	}

	/**
		Add an entry to a specific DN.

		@param $sDN The Distinguished Name.
		@param $aEntry Entry attributes for the specified DN.
		@return bool Whether the addition failed.
		@throw InvalidArgumentException $aEntry must be an array.
		@throw LDAPException If an error occurs.
	*/

	public function add($sDN, $aEntry)
	{
		is_array($aEntry) or burn('InvalidArgumentException', 'The `aEntry` parameter must be an array.');

		$b = ldap_add($this->rLink, $sDN, $aEntry); 
		//~ $b === false and burn('LDAPException', _WT('weeLDAP::add failed to add the entry attributes for the specifed DN.'));

		return $b;
	}

	/**
		Compares the value of the attribute of an entry for a specific DN.

		@param $sDN The Distinguished Name of an LDAP entity. 
		@param $sAttribute The attribute name.
		@param $sValue The compared value.
		@return bool Whether the value matched.
		@throw LDAPException If an error occurs.
	*/

	public function compare($sDN, $sAttribute, $sValue)
	{
		$b = ldap_compare($this->rLink, $sDN, $sAttribute, $sValue);
		$b === -1 and burn('LDAPException', sprintf(_WT('weeLDAP::compare failed for the value of "%s" attribute in the DN "%s".'), $sAttribute, $sDN));

		return $b;
	}

	/**
		Deletes an entry in the LDAP directory. 

		@param $sDN The Distinguished Name of an LDAP entity. 
		@return bool Whether the DN was deleted.
		@throw LDAPException If an error occurs.
	*/

	public function delete($sDN)
	{
		$b = ldap_delete($this->rLink, $sDN);
		//~ $b === false and burn('LDAPException', sprintf(_WT('weeLDAP::delete failed to delete "%s".'), $sDN));

		return $b;
	}

	/**
		Performs the search for a specified filter at the level immediately below the DN given in parameter, like the shell command ls.

		@param $sDN The Distinguished Name of an LDAP entity.
		@param $sFilter The filter for the search.
		@return weeLDAPResult The object containing the result.
		@throw LDAPException If an error occurs.
	*/

	public function ls($sDN, $sFilter)
	{
		$r = ldap_list($this->rLink, $sDN, $sFilter);
		$r === false and burn('LDAPException', sprintf(_WT('weeLDAP::ls failed to list "%s" with the filter "%s".'), $sDN, $sFilter));

		return new weeLDAPResult($this->rLink, $r);
	}

	/**
		Modify the existing entries in the LDAP directory.

		@param $sDN The Distinguished Name of an LDAP entity.
		@return bool Whether the modification was done.
		@throw LDAPException If an error occurs.
	*/

	public function update($sDN, $aEntry)
	{
		$b = ldap_modify($this->rLink, $sDN, $aEntry);
		$b === false and burn('LDAPException', sprintf(_WT('weeLDAP::modify failed to modify the specified entry in the DN "%s".'), $sDN));

		return $b;
	}

	/**
		Read an entry.

		@param $sDN The Distinguished Dame of an LDAP entity.
		@param $sFilter The filter for the read by default objectClass=*.
		@return weeLDAPResult The object containing the result.
	*/

	public function read($sDN, $sFilter = 'objectClass=*')
	{
		$r = ldap_read($this->rLink, $sDN, $sFilter);
		$r === false and burn('LDAPException', sprintf(_WT('weeLDAP::read failed to read an entry in the DN "%s" with the filter "%s".'), $sDN, $sFilter));

		return new weeLDAPResult($this->rLink, $r);
	}

	/**
		Perform the search for a specified filter in the entire directory.

		@param $sDN The Distinguished Name of an LDAP entity.
		@param $sFilter The filter for the search.
		@return weeLDAPResult The object containing the search result.
		@throw LDAPException If an error occurs.
	*/

	public function search($sDN, $sFilter)
	{
		$r = ldap_search($this->rLink, $sDN, $sFilter);
		$r === false and burn('LDAPException', sprintf(_WT('weeLDAP::search failed to perfom search with the specied filter in the DN "%s".'), $sDN));

		return new weeLDAPResult($this->rLink, $r);
	}
//TODO DN ou RDN?
	/**
		Rename the entry.

		@param $sFromDN The actual Distinguished Name.
		@param $sToDN The new Distinguished Name.
		@param $bDeleteOldRDN Whether the old DN has to be deleted.
		@return bool Wheter the DN was moved or renamed.
		@throw LDAPException If an error occurs.
	*/

	public function setDN($sFromDN, $sToDN, $bDeleteOldRDN = true)
	{
		$b	= ldap_rename($this->rLink, $sFromDN, $sToDN, null, $bDeleteOldRDN);
		$b	=== false	and burn('LDAPException', sprintf(_WT('weeLDAP::setDN failed to rename the entry "%s".'), $sFromDN));

		return $b;
	}
}
