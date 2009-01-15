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
	Authentication mechanisms against a table stored in a database.
*/

class weeAuthDbTable extends weeAuth
{
	/**
		Create a new weeAuthDbTable object and stores the paramters.

		Parameters:
			db:					The weeDatabase object to authenticate against.
			table:				The table containing the credentials to authenticate against.
			identifier_field:	The field containing the identifiers.
			password_field:		The field containing the passwords hashed with 'password_treatment'.
			password_treatment:	The function applied to each passwords stored in the 'password_field' field. Defaults to 'md5'.
			hash_treatment:		The PHP function to use to hash passwords stored client-side. Defaults to 'md5'.

		@param $aParams List of parameters to authenticate against.
	*/

	public function __construct($aParams = array())
	{
		empty($aParams['db']) || !($aParams['db'] instanceof weeDatabase) and burn('InvalidArgumentException',
			'You must provide a parameter "db" containing an instance of weeDatabase.');
		empty($aParams['table']) and burn('InvalidArgumentException',
			'You must provide a parameter "table" containing the name of the credentials table in your database.');
		empty($aParams['identifier_field']) and burn('InvalidArgumentException',
			'You must provide a parameter "identifier_field" containing the name of the field for the identifiers.');
		empty($aParams['password_field']) and burn('InvalidArgumentException',
			'You must provide a parameter "password_field" containing the name of the field for the passwords.');

		if (empty($aParams['password_treatment']))
			$aParams['password_treatment'] = 'md5';

		parent::__construct($aParams);
	}

	/**
		Authenticate using the provided credentials.

		Parameters:
			identifier:	The credentials identifier (like an username or an email).
			password:	The credentials password.

		@param $aCredentials Credentials used for authentication.
		@return array Data retrieved while authenticating. Contains the whole row retrieved from the database.
	*/

	public function authenticate($aCredentials)
	{
		return $this->doQuery($this->getDb()->bindNamedParameters(array('
			SELECT *
				FROM ' . $this->aParams['table'] . '
				WHERE	' . $this->aParams['identifier_field'] . '=:identifier
					AND	' . $this->aParams['password_field'] . '=' . $this->aParams['password_treatment'] . '(:password)
				LIMIT 1',
			$aCredentials
		)));
	}

	/**
		Authenticate using the provided credentials.
		The provided password was previously hashed using weeAuth::hash.

		Use this function along with weeAuth::hash when you need to store credentials client-side.

		Parameters:
			identifier:	The credentials identifier (like an username or an email).
			password:	The credentials password.

		@param $aCredentials Credentials used for authentication.
		@return array Data retrieved while authenticating. Contains the whole row retrieved from the database.
	*/

	public function authenticateHash($aCredentials)
	{
		defined('MAGIC_STRING') or burn('IllegalStateException',
			'You cannot hash a passphrase without defining the MAGIC_STRING constant first.');

		if ($this->getDb() instanceof weeMySQLDatabase)
			$sConcat = 'CONCAT(' . $this->aParams['password_field'] . ', ' . ':magic_string)';
		else
			$sConcat = $this->aParams['password_field'] . ' || :magic_string';

		return $this->doQuery($this->getDb()->bindNamedParameters(array('
			SELECT *
				FROM ' . $this->aParams['table'] . '
				WHERE	' . $this->aParams['identifier_field'] . '=:identifier
					AND	' . $this->aParams['hash_treatment'] . '(' . $sConcat . ')=:password
				LIMIT 1',
			array('magic_string' => MAGIC_STRING) + $aCredentials,
		)));
	}

	/**
		Performs the authentication using the given query.
		If the query finds 0 result, then the authentication failed and an AuthenticationException is thrown.
		If the query finds 2 or more results, an UnexpectedValueException is thrown.

		@param $sQuery Authentication query to perform.
		@return array Data retrieved while authenticating. Contains the whole row retrieved from the database.
	*/

	protected function doQuery($sQuery)
	{
		$oResults = $this->getDb()->query($sQuery);

		if (count($oResults) == 0)
			throw new AuthenticationException('The credentials provided were incorrect.');

		count($oResults) == 1 or burn('UnexpectedValueException',
			'The authentication query returned more than 1 result. Your table most likely contains dupes.');

		$aData = $oResults->fetch();
		unset($aData[$this->aParams['password_field']]);

		return $aData;
	}

	/**
		Returns the database associated to this authentication driver.

		@return weeDatabase The database associated to this authentication driver.
	*/

	public function getDb()
	{
		return $this->aParams['db'];
	}
}
