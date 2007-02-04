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
	Session management using a user's table in a database.
	Identify users against the database.
*/

class weeDatabaseSession extends weeSession
{
	/**
		The database object used to identify users.
	*/

	protected $oDatabase;

	/**
		The users table name in the database.
	*/

	protected $sUsersTable;

	/**
		The primary key column name for this table.
	*/

	protected $sKeyField;

	/**
		The login column name for this table.
	*/

	protected $sLoginField;

	/**
		The password column name for this table.
	*/

	protected $sPasswordField;

	/**
		Starts the session.

		If something seems wrong, reinitialize the session.
		If the user isn't logged in, but has cookies for autologin set, try to log in using cookies.

		@param	$oDatabase		The database object used to identify users.
		@param	$sUsersTable	The users table name in the database.
		@param	$sKeyField		The primary key column name for this table.
		@param	$sLoginField	The login column name for this table.
		@param	$sPasswordField	The password column name for this table. Passwords are stored using md5.
	*/

	public function __construct($oDatabase, $sUsersTable, $sKeyField, $sLoginField, $sPasswordField)
	{
		$this->oDatabase		= $oDatabase;
		$this->sUsersTable		= $sUsersTable;
		$this->sKeyField		= $sKeyField;
		$this->sLoginField		= $sLoginField;
		$this->sPasswordField	= $sPasswordField;

		parent::__construct();

		if (!$this->isLogged() && isset($_COOKIE['session_login'], $_COOKIE['session_password']))
			$this->cookieLogIn();

		unset($_COOKIE['session_login'], $_COOKIE['session_password']);
	}

	/**
		Tries to log in user using its cookies.
	*/

	protected function cookieLogIn()
	{
		$oResults	= $this->oDatabase->query('
			SELECT ' . $this->sKeyField . '
				FROM ' . $sUsersTable . '
				WHERE ' . $this->sLoginField . '=? AND MD5(' . $this->sPasswordField . ' || ?)=?
				LIMIT 1
		', $_COOKIE['session_login'], MAGIC_STRING, $_COOKIE['session_password']);

		$iUserId	= $this->validateLogInQuery($oResults);
		if (!is_null($iUserId))
			$this->processLogIn($iUserId, $bKeepAlive);
	}

	/**
		Returns whether user is logged in.

		@return bool Whether user is logged in.
	*/

	public function isLogged()
	{
		return !empty($_SESSION['session_is_logged']);
	}

	/**
		Checks if the session is invalid.

		The session is invalid if:
		 * the session's IP is empty
		 * the session's IP is different from the current user IP
		 * the session token is empty
		 * the session token is different from the cookie's session token

		Or:
		 * the user was logged in and it's account has been deleted

		@return bool True if the session is invalid, false otherwise.
	*/

	protected function isSessionInvalid()
	{
		if ($this->isLogged())
		{
			$aCount = $this->oDatabase->query('
				SELECT COUNT(*) AS c
					FROM ' . $this->sUsersTable . '
					WHERE ' . $this->sKeyField . '=?
					LIMIT 1
			', $_SESSION[$this->sKeyField])->fetch();

			if ($aCount['c'] == 0)
				return true;
		}

		return parent::isSessionInvalid();
	}

	/**
		Tries to log in user using the given login and password.

		If the $sKeepAlive parameter is set to true, creates cookies for automatic log in.

		@param	$sLogin		User's login.
		@param	$sPassword	User's password.
		@param	$bKeepAlive	Whether it must write cookies for automatic log in.
		@return	bool		Whether the user was logged in.
	*/

	public function logIn($sLogin, $sPassword, $bKeepAlive = false)
	{
		if ($_SESSION['session_is_logged'])
			return true;

		$oResults	= $this->oDatabase->query(
			'SELECT ' . $this->sKeyField . '
				FROM ' . $this->sUsersTable . '
				WHERE ' . $this->sLoginField . '=? AND ' . $this->sPasswordField . '=MD5(?)
				LIMIT 1
		', $sLogin, $sPassword);

		$iUserId	= $this->validateLogInQuery($oResults);
		if (is_null($iUserId))
			return false;

		$this->processLogIn($iUserId, $bKeepAlive);
		return true;
	}

	/**
		Creates a new session.
	*/

	protected function newSession()
	{
		parent::newSession();
		$_SESSION['session_is_logged'] = false;
	}

	/**
		Called when the user is logged in successfully.

		//TODO:public ?!

		@param	$iUserId	User's table primary key value. Unique identifier for this user.
		@param	$bKeepAlive	Whether it must write cookies for automatic log in.
	*/

	public function processLogIn($iUserId, $bKeepAlive = false)
	{
		session_regenerate_id();
		$_SESSION['session_is_logged']	= true;
		$_SESSION['session_ip']			= $this->getIP();
		$this->newToken();

		$aUser = $this->oDatabase->query('
			SELECT *
				FROM ' . $this->sUsersTable . '
				WHERE ' . $this->sKeyField . '=?
				LIMIT 1',
		$iUserId)->fetch();

		foreach ($aUser as $sKey => $sValue)
			$_SESSION[$sKey] = $sValue;
		unset($_SESSION[$this->sPasswordField]);

		if ($bKeepAlive)
		{
			weeOutput::setCookie('session_login',		$aUser[$this->sLoginField]);
			weeOutput::setCookie('session_password',	md5($aUser[$this->sPasswordField] . MAGIC_STRING));
		}
	}

	/**
		Returns whether the user was successfully logged in.

		This method returns null if the log in tentative failed.
		Otherwise it returns the user ID.

		@param	$oResults	A weeDatabaseResults object passed by cookieLogIn or logIn.
		@return	int			Null if log in failed, otherwise user's table primary key value (unique identifier for this user).
	*/

	protected function validateLogInQuery($oResults)
	{
		if ($oResults->numResults() == 0)
			return null;

		$a = $oResults->fetch();
		return $a[$this->sKeyField];
	}
}

?>
