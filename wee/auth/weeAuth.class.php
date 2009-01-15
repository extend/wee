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
	Base class for authentication mechanisms.
*/

abstract class weeAuth
{
	/**
		Contains the target for authentication along with any other parameter.
	*/

	protected $aParams;

	/**
		Create a new weeAuth object and stores the paramters.

		Parameters:
			hash_treatment: The PHP function to use to hash passwords stored client-side. Defaults to 'md5'.

		@param $aParams List of parameters to authenticate against.
	*/

	public function __construct($aParams = array())
	{
		if (empty($aParams['hash_treatment']))
			$aParams['hash_treatment'] = 'md5';

		$this->aParams = $aParams;
	}

	/**
		Authenticate using the provided credentials.

		@param $aCredentials Credentials used for authentication.
		@return array Data retrieved while authenticating. Contents depends on the driver.
	*/

	abstract public function authenticate($aCredentials);

	/**
		Authenticate using the provided credentials.
		The provided password was previously hashed using weeAuth::hash.

		Use this function along with weeAuth::hash when you need to store credentials client-side.

		@param $aCredentials Credentials used for authentication.
		@return array Data retrieved while authenticating. Contents depends on the driver.
	*/

	abstract public function authenticateHash($aCredentials);

	/**
		Hash a password in order to store it client-side.

		@param $sPassword Password to be hashed.
		@return string Hashed password.
	*/

	public function hash($sPassword)
	{
		defined('MAGIC_STRING') or burn('IllegalStateException',
			'You cannot hash a password without defining the MAGIC_STRING constant first.');

		$sFunc = $this->aParams['hash_treatment'];
		return $sFunc($sFunc($sPassword) . MAGIC_STRING);
	}
}
