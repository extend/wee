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

abstract class weeOutput implements Singleton
{
	protected $bGzipped;
	protected static $sCookiePath;
	protected static $oSingleton;

	protected function __construct()
	{
		$s					= str_replace(', ', ',', $_SERVER['HTTP_ACCEPT_ENCODING']);
		$aAcceptEncoding	= explode(',', $s);
		$this->bGzipped		= in_array('gzip', $aAcceptEncoding);

		self::$sCookiePath		= str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
		$s						= ROOT_PATH;
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

	final private function __clone()
	{
	}

	public static function deleteCookie($sName)
	{
		Fire(headers_sent(), 'IllegalStateException');
		setcookie($sName, '', 0, self::$sCookiePath);
	}

	public static function encodeValue($mValue)
	{
		Fire(empty(self::$oSingleton), 'IllegalStateException');
		return self::$oSingleton->encode($mValue);
	}

	abstract public function encode($mValue);

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

	public static function header($sString, $bReplace = true)
	{
		Fire(headers_sent(), 'IllegalStateException');
		header($sString, $bReplace);
	}

	public function isGzipped()
	{
		return $this->bGzipped;
	}

	public static function setCookie($sName, $sValue, $iExpire = null)
	{
		Fire(headers_sent(), 'IllegalStateException');

		if (is_null($iExpire))
			$iExpire = time() + 2592000; // 30 days from now
		setcookie($sName, $sValue, $iExpire, self::$sCookiePath);
	}
}

?>
