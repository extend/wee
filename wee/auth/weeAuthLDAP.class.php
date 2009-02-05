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
	Authentication for LDAP servers.
*/

class weeAuthLDAP extends weeAuth
{
	/**
		Create a new weeAuthLDAP object and store the paramters.

		Parameters:
			ldap: The weeLDAP object getted after authentication to the LDAP server.
			base_dn: The base DN for making search.
			password_treatment: The callback applied to the password value of the 'userPassword' attribute of the entry. Defaults to 'md5'.
			hash_treatment: The callback to use to hash passwords stored client-side. Defaults to 'md5'.

		@param $aParams List of parameters to authenticate against.
	*/

	public function __construct($aParams = array())
	{
		empty($aParams['ldap']) and burn('InvalidArgumentException',
			_WT('You must provide a parameter "ldap" containing the instance of weeLDAP.'));
		empty($aParams['base_dn']) and burn('InvalidArgumentException',
			_WT('You must provide a parameter "base_dn" containing the base DN.')); //TODO message

		if (empty($aParams['password_treatment']))
			$aParams['password_treatment'] = 'md5';
		else
			is_callable($aParams['password_treatment'])
				or burn('InvalidArgumentException', _WT('The "password_treatment" parameter must be a valid callback.'));

		parent::__construct($aParams);
	}

	/**
		Authenticate using the provided credentials.

		Parameters:
			identifier:	The credentials identifier (cn).
			password:	The credentials password.

		@param $aCredentials Credentials used for authentication.
		@return weeLDAPEntry Data retrieved while authenticating. Contains the attributes and values of the specified cn.
	*/

	public function authenticate($aCredentials)
	{
		$sFunc = $this->aParams['password_treatment'];
		$aCredentials['hash'] = $sFunc($aCredentials['password']);
		unset($aCredentials['password']);

		$oResults = $this->aParams['ldap']->search($this->aParams['base_dn'], 'cn='. $aCredentials['identifier']);
		if (count($oResults) === 0)
			throw new AuthenticationException('The credentials provided were incorrect.');

		count($oResults) === 1 or burn('UnexpectedValueException',
			_WT('The authentication query returned more than 1 result.'));

		$oEntry = $oResults->fetch();
		if ($oEntry['userPassword'][0] !== $aCredentials['hash'])
			throw new AuthenticationException('The credentials provided were incorrect.');

		unset($oEntry['userPassword']);
		return $oEntry;
	}

	/**
		Authenticate using the provided credentials.
		The provided password was previously hashed using weeAuth::hash.

		Use this function along with weeAuth::hash when you need to store credentials client-side.

		Parameters:
			identifier: The credentials identifier (cn).
			password: The credentials password.

		@param $aCredentials Credentials used for authentication.
		@return weeLDAPEntry Data retrieved while authenticating. Contains the attributes and values of the specified cn.
	*/

	public function authenticateHash($aCredentials)
	{
		$oResults = $this->aParams['ldap']->search($this->aParams['base_dn'], 'cn='. $aCredentials['identifier']);

		count($oResults) !== 0 or burn('AuthenticationException',
			_WT('The provided credentials were incorrect.'));

		count($oResults) === 1 or burn('UnexpectedValueException',
			_WT('The authentication query returned more than 1 result.'));

		$oEntry = $oResults->fetch();
		$sFunc = $this->aParams['hash_treatment'];

		$aCredentials['password'] === $sFunc($oEntry['userPassword'][0] . MAGIC_STRING)
			or burn('AuthenticationException', _WT('The provided credentials were incorrect.'));

		unset($oEntry['userPassword']);
		return $oEntry;
	}
}
