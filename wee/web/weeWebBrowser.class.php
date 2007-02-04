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
	Tentatively emulates a web browser.

	@todo digest identification
	@todo headers
*/

class weeWebBrowser
{
	/**
		Path to the cookie file.
	*/

	protected $sCookieFile;

	/**
		Initialize the web browser.

		@param $sCookieFile The path to the cookie file.
	*/

	public function __construct($sCookieFile)
	{
		fire(file_exists($sCookieFile) && !(is_readable($sCookieFile) && is_writable($sCookieFile)), 'NotPermittedException');
		$this->sCookieFile = $sCookieFile;
	}

	/**
	*/

	public function cookie($sName, $sValue)
	{
	}

	/**
		Fetch the data from the specified URL and return it.

		@param	$sURL	URL to fetch
		@param	$aPost	Values to pass using POST
		@return	string	Contents downloaded from URL
	*/

	public function fetch($sURL, $aPost = array())
	{
		$rCurl = curl_init();

		curl_setopt($rCurl, CURLOPT_COOKIEJAR, $this->sCookieFile);
		curl_setopt($rCurl, CURLOPT_COOKIEFILE, $this->sCookieFile);
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($rCurl, CURLOPT_URL, $sURL);

		if (!empty($aPost))
		{
			$sPostFields = null;
			foreach ($aPost as $sName => $sValue)
				$sPostFields .= urlencode($sName) . '=' . urlencode($sValue) . '&';

			curl_setopt($rCurl, CURLOPT_POST, 1);
			curl_setopt($rCurl, CURLOPT_POSTFIELDS, substr($sPostFields, 0, -1));
		}

		$sRet = curl_exec($rCurl);
		curl_close($rCurl);

		return $sRet;
	}

	/**
		Fetch the data from the specified URL and return it as a weeWebDocument object.

		@param	$sURL	URL to fetch
		@param	$aPost	Values to pass using POST
		@return	object	Contents downloaded from URL returned as weeWebDocument
	*/

	public function fetchDoc($sURL, $aPost = array())
	{
		return simplexml_load_string($this->fetch($sURL, $aPost), 'weeWebDocument', LIBXML_DTDLOAD);
	}
}

?>
