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
	Wrapper around the $_COOKIE array.

	Allows you to retrieve cookies from the array or to set and delete cookies.
	The class can be used as an array, with a few nuances when unsetting or setting cookies.
*/

class weeCookies implements ArrayAccess
{
	/**
		Path used when setting or deleting cookies.
	*/

	protected $sCookiePath;

	/**
		Initialize the cookies class.

		An optional single parameter is allowed:
		* path: cookie path used when setting or deleting the cookies

		@param $aParams A list of parameters to configure the cookies class.
	*/

	public function __construct($aParams = array())
	{
		$this->sCookiePath = empty($aParams['path']) ? $this->getDefaultPath() : $aParams['path'];
	}

	/**
		Return the default cookie path.

		This method will return APP_PATH if a custom APP_PATH has been defined.
		Otherwise it will return the full path to the application.

		@return Default cookie path.
		@todo This might be nice at a later time to just have APP_PATH use this form and use it directly.
	*/

	protected function getDefaultPath()
	{
		if (APP_PATH != BASE_PATH && APP_PATH != BASE_PATH . ROOT_PATH)
			return APP_PATH; // A custom APP_PATH was defined, use it

		$iCount = substr_count(substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME'])), '/')
			- (int)(isset($_SERVER['REDIRECT_URL']));
		$sPath = str_replace('\\', '/', dirname((isset($_SERVER['REDIRECT_URL'])) ? $_SERVER['REDIRECT_URL'] : $_SERVER['PHP_SELF']));

		for ($i = 0; $i < $iCount; $i++)
			$sPath = dirname($sPath);

		if (substr($sPath, -1) != '/')
			$sPath .= '/';

		return $sPath;
	}

	/**
		Return whether offset exists.

		@param	$offset	Offset name.
		@return	bool	Whether the offset exists.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetExists($offset)
	{
		return isset($_COOKIE[$offset]);
	}

	/**
		Return value at given offset.

		@param	$offset	Offset name.
		@return	mixed	Value at given offset
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetGet($offset)
	{
		array_key_exists($offset, $_COOKIE) or burn('InvalidArgumentException',
			sprintf(_WT('The cookie named "%s" has not been received.'), $offset));
		return $_COOKIE[$offset];
	}

	/**
		Send a cookie to the browser.

		This aliases weeCookies::set with a default 3rd parameter.
		This do NOT add the value directly in the cookies array.
		The value is only accessible on the next request from this user.

		@param	$offset	Offset name.
		@param	$value	New value for this offset.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
		Delete the specified cookie.

		This do NOT remove the value directly from the cookies array.
		The value will be deleted on the next request from this user.

		@param	$offset	Offset name.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetUnset($offset)
	{
		headers_sent() and burn('IllegalStateException',
			_WT('You cannot delete a cookie if headers are already sent.'));

		setcookie($offset, '', 0, $this->sCookiePath);
	}

	/**
		Send a cookie to the browser.

		@param $sName	Name of the cookie.
		@param $sValue	Value of the cookie.
		@param $iExpire	Expiration time (UNIX timestamp, in seconds).
	*/

	public function set($sName, $sValue, $iExpire = 0)
	{
		headers_sent() and burn('IllegalStateException',
			_WT('You cannot set a cookie if headers are already sent.'));

		setcookie($sName, $sValue, $iExpire, $this->sCookiePath);
	}
}
