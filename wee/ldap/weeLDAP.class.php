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
		Establish a simple connection to an LDAP server on a specified hostname and port, and bind to the LDAP directory with specified RDN and password.
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
		$this->rLink === false and burn('LDAPException', 
			sprintf(_WT('Can not connect to "%s" :'), array_value($aParams,'host')));

		$b = ldap_bind($this->rLink, array_value($aParams, 'rdn', null), array_value($aParams, 'password', null));
		if ($b === false)
			throw new LDAPException(
				sprintf(_WT('Can not bind the RDN "%s".'), array_value($aParams,'rdn'), null) . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);
	}
 
	/**
		Escape the value of the component of a DN string.

		@param $sValue The string to escape.
		@return string The escaped string.
	*/

	public function escape($sValue)
	{
		if (empty($sValue)) return $sValue;

		$sValue = str_replace(
			array('\\', '+', '"', '>', '<', ';', ','), 
			array('\\\\', '\+', '\"',  '\>', '\<', '\;', '\,'),
			$sValue
		);

		if ($sValue[strlen($sValue) - 1] === ' ') {
			$sValue[strlen($sValue) - 1] = '\\';
			$sValue .= ' ';
		}

		if ($sValue[0] === ' ' || $sValue[0] === '#')
			$sValue = '\\' . $sValue;

		return $sValue;
	}

	/**
		Escape the given filter.

		@param $sFilter The filter to escape.
		@return string The escaped filter.
	*/

	public function escapeFilter($sFilter)
	{
		if (empty($sFilter)) return $sFilter;

		$sFilter = str_replace(
			array('\\', '*', '(', ')', "\0"), 
			array('\5C', '\2A', '\28', '\29', '\00'),
			$sFilter
		);

		return $sFilter;
	}

	/**
		Copy the entry.

		@param $sFromDN The actual Distinguished Name.
		@param $sToDN The new Distinguished Name.
		@throw InvalidArgumentException The DNs must be different.
	*/

	public function copy($sFromDN, $sToDN)
	{
		$sFromDN === $sToDN and burn('InvalidArgumentException', 
			'The DN source and destination must be different');

		$aFromDN = split(',', $sFromDN, 2);
		$oFromEntry = $this->search($aFromDN[1], $aFromDN[0], false)->fetch();

		$this->insert($sToDN, $oFromEntry->toArray());
	}

	/**
		Delete an entry in the LDAP directory.

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
		Read an entry.

		@param $sDN The Distinguished Name of an LDAP entity.
		@param $sFilter The filter for the read by default objectClass=*.
		@return weeLDAPEntry An instance of weeLDAPEntry.
	*/

	public function fetch($sDN, $sFilter = 'objectClass=*')
	{
		$r = ldap_read($this->rLink, $sDN, $sFilter);
		if ($r === false)
			throw new LDAPException(
				_WT('Failed to read in the specified DN.') . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);

		$rEntry = ldap_first_entry($this->rLink, $r);
		if ($rEntry === false)
			throw new LDAPException(
				_WT('Failed to get the first entry.') . "\n" . ldap_error($this->rLink), 
				ldap_errno($this->rLink)
			);

		return new weeLDAPEntry($this->rLink, $rEntry);
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
		Compare the value of the attribute of an entry for a specific DN.

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
		Modify the existing entries in the LDAP directory.

		@param $sDN The Distinguished Name of an LDAP entity.
		@param $aEntry Entry attributes for the specified DN.
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
		Move the entry.

		@param $sFromDN The actual Distinguished Name.
		@param $sToDN The new Distinguished Name.
		@throw ConfigurationException The server must be configured to use LDAPv3.
		@throw UnexpectedValueException If an error occurs.
		@throw LDAPException If an error occurs.
	*/

	public function move($sFromDN, $sToDN)
	{
		if (ldap_get_option($this->rLink, LDAP_OPT_PROTOCOL_VERSION, $iVersion)) {
				$iVersion === 3 or burn('ConfigurationException', 
					'This feature works only with LDAPv3. You may have to set the protocol version option prior to binding, to use LDAPv3.');
		} else
			throw new LDAPException(
				_WT('Failed to get the protocol version.') . "\n" . ldap_error($this->rLink),
				ldap_errno($this->rLink)
			);

		$aToRDN = split(',', $sToDN, 2);
		$b = ldap_rename($this->rLink, $sFromDN, $aToRDN[0], $aToRDN[1], true);
		if ($b === false)
			throw new LDAPException(
				_WT('Failed to move the entry.') . "\n" . ldap_error($this->rLink), 
				ldap_errno($this->rLink)
			);
	}

	/**
		Rename the entry.

		@param $sFromDN The actual Distinguished Name.
		@param $sToRDN The new Relative Distinguished Name.
		@throw LDAPException If an error occurs.
	*/

	public function rename($sFromDN, $sToRDN)
	{
		$b = ldap_rename($this->rLink, $sFromDN, $sToRDN, null, true);
		if ($b === false)
			throw new LDAPException(
				_WT('Failed to rename the entry.') . "\n" . ldap_error($this->rLink), 
				ldap_errno($this->rLink)
			);
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
}
