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
	Base class for output drivers.
*/

abstract class weeOutput implements Singleton
{
	/**
		True if output will be gzipped, false otherwise.
	*/

	protected $bGzipped;

	/**
		Path used in the deleteCookie and setCookie methods.
		Currently determined automatically in the constructor.
	*/

	protected static $sCookiePath;

	/**
		Instance of the current output driver.
		There can only be one.
	*/

	protected static $oSingleton;

	/**
		Initialize the output driver.

		Checks if gzip compression is supported, determines the cookie path, and initialize output buffering.
	*/

	protected function __construct()
	{
		if (empty($_SERVER['HTTP_ACCEPT_ENCODING']))
			$this->bGzipped		= false;
		else
		{
			$s					= str_replace(', ', ',', $_SERVER['HTTP_ACCEPT_ENCODING']);
			$aAcceptEncoding	= explode(',', $s);
			$this->bGzipped		= in_array('gzip', $aAcceptEncoding);
		}

		self::$sCookiePath		= str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
		$s						= APP_PATH;
		while (substr($s, 0, 3) == '../')
		{
			self::$sCookiePath	= dirname(self::$sCookiePath);
			$s					= substr($s, 3);
		}
		if (substr(self::$sCookiePath, strlen(self::$sCookiePath) - 1) != '/')
			self::$sCookiePath .= '/';

		if (ini_get('output_buffering') || !$this->bGzipped || ini_get('zlib.output_compression'))
			ob_start();
		else
		{
			$this->header('Content-Encoding: gzip');
			ob_start('ob_gzhandler');
		}
	}

	/**
		Because there can only be one output driver, we disable cloning.
	*/

	final private function __clone()
	{
	}

	/**
		Delete the specified cookie.

		@param $sName Name of the cookie to delete.
	*/

	public static function deleteCookie($sName)
	{
		fire(headers_sent(), 'IllegalStateException');
		setcookie($sName, '', 0, self::$sCookiePath);
	}

	/**
		Encodes data to be displayed.

		This method redirects the call to the singleton encode method.
		It is used for example inside Web:Extend itself since we can't know what drivers the program is using.
		You should not have to use this method.

		@param	$mValue	Data to encode.
		@return	string	Data encoded.
	*/

	public static function encodeValue($mValue)
	{
		fire(empty(self::$oSingleton), 'IllegalStateException');
		return self::$oSingleton->encode($mValue);
	}

	/**
		Encodes data to be displayed.

		@param	$mValue	Data to encode.
		@return	string	Data encoded.
	*/

	abstract public function encode($mValue);

	/**
		Encodes an array of data to be displayed.

		Mainly used by weeTemplate to encode the data it received.
		You should not have to use this method.

		@param	$a		Data array to encode.
		@return	array	Data array encoded.
	*/

	public static function encodeArray(&$a)
	{
		foreach ($a as $mName => $mValue)
		{
			if ($mValue instanceof weeDatabaseResult || $mValue instanceof weeDatabaseRow)
				$a[$mName] = $mValue->encodeResults();
			elseif (is_object($mValue))
				continue;
			elseif (is_array($mValue))
				$a[$mName] = self::encodeArray($mValue);
			else
				$a[$mName] = self::encodeValue($mValue);
		}
		return $a;
	}

	/**
		Sends a header to the browser.

		Tentatively prevent HTTP Response Splitting.

		@param $sString		Header string.
		@param $bReplace	Replace existing header if true.
	*/

	public static function header($sString, $bReplace = true)
	{
		fire(headers_sent(), 'IllegalStateException');
		fire(strpos($sString, "\r") !== false || strpos($sString, "\n") !== false, 'UnexpectedValueException');
		header($sString, $bReplace);
	}

	/**
		Tells if output will be gzipped or not.

		@return bool True if output is gzip encoded, false otherwise.
	*/

	public function isGzipped()
	{
		return $this->bGzipped;
	}

	/**
		Sends a cookie to the browser.
		The default expire delay is 30 days.

		@param $sName	Name of the cookie.
		@param $sValue	Value of the cookie.
		@param $iExpire	Expiration time (UNIX timestamp, in seconds).
	*/

	public static function setCookie($sName, $sValue, $iExpire = null)
	{
		fire(headers_sent(), 'IllegalStateException');

		if (is_null($iExpire))
			$iExpire = time() + 2592000; // 30 days from now
		setcookie($sName, $sValue, $iExpire, self::$sCookiePath);
	}
}

?>
