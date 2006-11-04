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

class weeDatabaseSession extends weeSession
{
	protected $oDatabase;
	protected $sUsersTable;
	protected $sKeyField;
	protected $sLoginField;
	protected $sPasswordField;

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

	protected function cookieLogIn()
	{
		$oQuery		= $this->oDatabase->Query('SELECT ' . $this->sKeyField . ' FROM ' . $sUsersTable . ' WHERE ' .
			$this->sLoginField . '=? AND MD5(' . $this->sPasswordField . ' || ?)=? LIMIT 1',
			$_COOKIE['session_login'], MAGIC_STRING, $_COOKIE['session_password']);

		$iUserId	= $this->validateLogInQuery($oQuery);
		if (!is_null($iUserId))
			$this->processLogIn($iUserId, $bKeepAlive);
	}

	public function isLogged()
	{
		return !empty($_SESSION['session_is_logged']);
	}

	protected function isSessionInvalid()
	{
		if ($this->isLogged())
		{
			$oQuery	= $this->oDatabase->Query('SELECT COUNT(*) AS c FROM ' . $this->sUsersTable . ' WHERE ' . $this->sKeyField . '=? LIMIT 1', $_SESSION[$this->sKeyField]);
			$aCount	= $oQuery->Fetch();

			if ($aCount['c'] == 0)
				return true;
		}

		return parent::isSessionInvalid();
	}

	public function logIn($sLogin, $sPassword, $bKeepAlive = false)
	{
		if ($_SESSION['session_is_logged'])
			return true;

		$oQuery		= $this->oDatabase->Query('SELECT ' . $this->sKeyField . ' FROM ' . $this->sUsersTable . ' WHERE ' .
			$this->sLoginField . '=? AND ' . $this->sPasswordField . '=MD5(?) LIMIT 1', $sLogin, $sPassword);

		$iUserId	= $this->validateLogInQuery($oQuery);
		if (is_null($iUserId))
			return false;

		$this->processLogIn($iUserId, $bKeepAlive);
		return true;
	}

	protected function newSession()
	{
		parent::newSession();
		$_SESSION['session_is_logged'] = false;
	}

	public function processLogIn($iUserId, $bKeepAlive = false)
	{
		session_regenerate_id();
		$_SESSION['session_is_logged']	= true;
		$_SESSION['session_ip']			= $this->getIP();
		$this->newToken();

		$oQuery	= $this->oDatabase->Query('SELECT * FROM ' . $this->sUsersTable . ' WHERE ' . $this->sKeyField . '=? LIMIT 1', $iUserId);
		$aUser	= $oQuery->Fetch();

		foreach ($aUser as $sKey => $sValue)
			$_SESSION[$sKey] = $sValue;
		unset($_SESSION[$this->sPasswordField]);

		if ($bKeepAlive)
		{
			weeOutput::setCookie('session_login',		$aUser[$this->sLoginField]);
			weeOutput::setCookie('session_password',	md5($aUser[$this->sPasswordField] . MAGIC_STRING));
		}
	}

	protected function validateLogInQuery($oQuery)
	{
		if ($oQuery->numResults() == 0)
			return null;

		$a = $oQuery->fetch();
		return $a[$this->sKeyField];
	}
}

?>
