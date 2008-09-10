<?php

/*
	Web:Extend
	Copyright (c) 2006, 2008 Dev:Extend

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
	Wrapper for easier session management.
*/

class weeSession implements ArrayAccess
{
	/**
		Starts the session.

		If something seems wrong (isSessionInvalid returns true, or the session name is bad), reinitialize the session.
	*/

	public function __construct()
	{
		fire(session_id() != '', 'IllegalStateException',
			'A session already exist. You cannot create a new weeSession if a PHP session is active.');

		// Sanitize session id, then start session

		if (isset($_COOKIE[session_name()]) && !preg_match('/^[a-z0-9-]+$/is', $_COOKIE[session_name()]))
			unset($_COOKIE[session_name()]);
		session_start();

		if (!empty($_SESSION) && $this->isSessionInvalid())
			$this->clear();

		if (empty($_SESSION))
			$this->initSession();
	}

	/**
		Closes the session.
	*/

	public function __destruct()
	{
		session_write_close();
	}

	/**
		Deletes session and create a new, empty one.
	*/

	public function clear()
	{
		$_SESSION = array();
		session_regenerate_id();

		weeOutput::setCookie(session_name(), '');
		unset($_COOKIE[session_name()]);

		$this->initSession();
	}

	/**
		Returns the session's user IP.
		If user is behind a proxy, returns the forwarded IP.

		@return string The IP for this session.
	*/

	public function getIP()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
		Initialize the session if required.

		If WEE_SESSION_CHECK_IP is defined, store the IP into the session.
		If WEE_SESSION_CHECK_TOKEN is defined, generate a token and store
		it both into the session and in the client's cookies.
	*/

	protected function initSession()
	{
		if (defined('WEE_SESSION_CHECK_IP'))
			$_SESSION['session_ip'] = $this->getIP();

		if (defined('WEE_SESSION_CHECK_TOKEN'))
		{
			fire(!defined('MAGIC_STRING'), 'IllegalStateException',
				'You cannot use token session protection without defining the MAGIC_STRING constant first.');

			$_SESSION['session_token'] = substr(md5(rand() . MAGIC_STRING), 0, 8) . substr(md5(time() . MAGIC_STRING), 0, 8);
			weeOutput::setCookie('session_token', $_SESSION['session_token']);
		}
	}

	/**
		Checks if the session is invalid.

		The session is invalid if:
		 * WEE_SESSION_CHECK_IP is defined and either of
			 * the session's IP is empty
			 * the session's IP is different from the current user IP
		 * WEE_SESSION_CHECK_TOKEN is defined and either of
			 * the session token is empty
			 * the session token is different from the cookie's session token

		@return bool True if the session is invalid, false otherwise.
	*/

	protected function isSessionInvalid()
	{
		if (defined('WEE_SESSION_CHECK_IP') &&
			(empty($_SESSION['session_ip']) || $this->getIP() != $_SESSION['session_ip']))
			return true;

		if (defined('WEE_SESSION_CHECK_TOKEN') &&
			(empty($_COOKIE['session_token']) || empty($_SESSION['session_token']) || $_SESSION['session_token'] != $_COOKIE['session_token']))
			return true;

		return false;
	}

	/**
		Returns whether offset exists.

		@param	$offset	Offset name.
		@return	bool	Whether the offset exists.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetExists($offset)
	{
		return isset($_SESSION[$offset]);
	}

	/**
		Returns value at given offset.

		@param	$offset	Offset name.
		@return	mixed	Value at given offset
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetGet($offset)
	{
		fire(!array_key_exists($offset, $_SESSION), 'InvalidArgumentException',
			'There is no value stored in the session for the offset ' . $offset . '.');
		return $_SESSION[$offset];
	}

	/**
		Sets a new value for the given offset.

		@param	$offset	Offset name.
		@param	$value	New value for this offset.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetSet($offset, $value)
	{
		$_SESSION[$offset] = $value;
	}

	/**
		Unsets offset.

		@param	$offset	Offset name.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetUnset($offset)
	{
		unset($_SESSION[$offset]);
	}

	/**
		Copy data directly from an array.

		@param $aData Array containing the data to copy from.
	*/

	public function setFromArray($aData)
	{
		is_array($aData)
			or burn('InvalidArgumentException',
				_('$aData is not an array.'));

		foreach ($aData as $sKey => $mValue)
			$this[$sKey] = $mValue;
	}
}
