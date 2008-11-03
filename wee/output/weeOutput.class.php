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
	Base class for output drivers.
*/

abstract class weeOutput
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

	protected static $oInstance;

	/**
		Because there can only be one output driver, we disable cloning.
	*/

	final private function __clone()
	{
	}

	/**
		Decodes a given value.

		@param	$mValue	The value to decode.
		@return	string	The decoded value.
	*/

	public abstract function decode($mValue);

	/**
		Delete the specified cookie.

		@param $sName Name of the cookie to delete.
	*/

	public static function deleteCookie($sName)
	{
		fire(headers_sent(), 'IllegalStateException', 'You cannot delete a cookie if headers are already sent.');

		if (empty(self::$sCookiePath))
			self::initCookiePath();

		setcookie($sName, '', 0, self::$sCookiePath);
	}

	/**
		Encodes data to be displayed.

		This method redirects the call to the output encode method.
		It is used for example inside Web:Extend itself since we can't know what drivers the program is using.
		You should not have to use this method.

		@param	$mValue	Data to encode.
		@return	string	Data encoded.
	*/

	public static function encodeValue($mValue)
	{
		fire(empty(self::$oInstance), 'IllegalStateException',
			'An instance of weeOutput must be selected before calling weeOutput::encodeValue.' .
			' You should select it before doing any code relating to output, be forms or templates.');

		return self::$oInstance->encode($mValue);
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
			if ($mValue instanceof weeDataSource)
				$a[$mName] = $mValue->encodeData();
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
		fire(headers_sent(), 'IllegalStateException',
			'You cannot add another header to be sent to browser if they are already sent.');
		fire(strpos($sString, "\r") !== false || strpos($sString, "\n") !== false, 'UnexpectedValueException',
			'Line breaks are not allowed in headers to prevent HTTP Response Splitting.');
		fire(strpos($sString, "\0") !== false, 'UnexpectedValueException',
			'Null characters are not allowed in headers.');

		header($sString, $bReplace);
	}

	/**
		Determine the cookie path.
		Called only once the first time setCookie or deleteCookie is called.

		@todo We should have some common code for handling the request uri and various base path
	*/

	protected static function initCookiePath()
	{
		$sRequestURI		= (isset($_SERVER['REDIRECT_URL'])) ? $_SERVER['REDIRECT_URL'] : $_SERVER['PHP_SELF'];
		self::$sCookiePath	= str_replace('\\', '/', dirname($sRequestURI));
		$s					= APP_PATH;
		while (substr($s, 0, 3) == '../')
		{
			self::$sCookiePath	= dirname(self::$sCookiePath);
			$s					= substr($s, 3);
		}
		//TODO:this part looks useless, to be tested and confirmed
		if (substr(self::$sCookiePath, strlen(self::$sCookiePath) - 1) != '/')
			self::$sCookiePath .= '/';
	}

	/**
		Return the currently selected instance.
		Throw an exception if no instances are selected.

		@return weeOutput The selected output instance.
	*/

	public static function instance()
	{
		return self::$oInstance;
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
		fire(headers_sent(), 'IllegalStateException', 'You cannot set a cookie if headers are already sent.');

		if (empty(self::$sCookiePath))
			self::initCookiePath();

		if (is_null($iExpire))
			$iExpire = time() + 2592000; // 30 days from now
		setcookie($sName, $sValue, $iExpire, self::$sCookiePath);
	}

	/**
		Set the output object to use.

		@param $oInstance The object to use.
	*/

	public static function setInstance(weeOutput $oInstance)
	{
		self::$oInstance = $oInstance;
	}

	/**
		Start the output.
		Checks if gzip compression is supported and initialize output buffering.

		@param $bGzipOutput Whether to gzip the output before sending it to the browser (if available).
	*/

	public function start($bGzipOutput = true)
	{
		if (!$bGzipOutput || empty($_SERVER['HTTP_ACCEPT_ENCODING']))
			$this->bGzipped		= false;
		else
		{
			$s					= str_replace(', ', ',', $_SERVER['HTTP_ACCEPT_ENCODING']);
			$aAcceptEncoding	= explode(',', $s);
			$this->bGzipped		= in_array('gzip', $aAcceptEncoding);
		}

		if (ini_get('output_buffering') || !$this->bGzipped || ini_get('zlib.output_compression'))
			ob_start();
		else
		{
			$this->header('Content-Encoding: gzip');
			ob_start('ob_gzhandler');

			// Flag indicating we sent a gzip header
			define('WEE_GZIP', 1);
		}
	}
}
