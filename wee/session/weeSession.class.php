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

if (!defined('MAGIC_STRING'))
	define('MAGIC_STRING', 'Our dreams are lost in the flow of time, still we are looking for the future in this wired world...');

class weeSession implements ArrayAccess
{
	public function __construct()
	{
		fire(session_id() != '', 'IllegalStateException');

		// Sanitize session id, then start session

		if (isset($_COOKIE[session_name()]) && !preg_match('/^[a-z0-9-]+$/is', $_COOKIE[session_name()]))
			unset($_COOKIE[session_name()]);
		session_start();

		if (!empty($_SESSION) && $this->isSessionInvalid())
			$this->logOut();

		if (empty($_SESSION))
			$this->newSession();
	}

	public function __destruct()
	{
		session_write_close();
	}

	protected function getIP()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		return $_SERVER['REMOTE_ADDR'];
	}

	protected function isSessionInvalid()
	{
		return (empty($_SESSION['session_ip']) ||
				$this->getIP() != $_SESSION['session_ip'] ||
				empty($_COOKIE['session_token']) ||
				$_SESSION['session_token'] != $_COOKIE['session_token']);
	}

	public function logOut()
	{
		$_SESSION = array();
		session_regenerate_id();

		weeOutput::setCookie(session_name(),	'');
		unset($_COOKIE['session_name']);

		$this->newSession();
	}

	protected function newSession()
	{
		$_SESSION['session_ip'] = $this->getIP();
		$this->newToken();
	}

	protected function newToken()
	{
		$_SESSION['session_token'] = substr(md5(rand() . MAGIC_STRING), 0, 8) . substr(md5(time() . MAGIC_STRING), 0, 8);
		weeOutput::setCookie('session_token', $_SESSION['session_token']);
	}

	public function offsetExists($offset)
	{
		return isset($_SESSION[$offset]);
	}

	public function offsetGet($offset)
	{
		if (array_key_exists($offset, $_SESSION))
			return $_SESSION[$offset];
		return null;
	}

	public function offsetSet($offset, $value)
	{
		$_SESSION[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($_SESSION[$offset]);
	}
}

?>
