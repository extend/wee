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
		@todo Array of values in $aPost
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

		When querying the weeWebDocument returned using xpath, you must use the namespace "html"
		to access elements in the default namespace. Thus if you want to get all the divs of the
		document, you must use "//html:div".

		@param	$sURL	URL to fetch
		@param	$aPost	Values to pass using POST
		@return	object	Contents downloaded from URL returned as weeWebDocument
		@todo registerXPathNamespace does not exists for PHP < 5.1
	*/

	public function fetchDoc($sURL, $aPost = array())
	{
		$sDoc		= $this->fetch($sURL, $aPost);

		$iXMLNSPos	= strpos($sDoc, 'xmlns="');
		if ($iXMLNSPos === false)
			$sXMLNS	= ''; //TODO:not sure if this helps
		else
		{
			$iXMLNSPos += 7;
			$sXMLNS	= substr($sDoc, $iXMLNSPos, strpos($sDoc, '"', $iXMLNSPos) - $iXMLNSPos);
		}

		$oXML = simplexml_load_string($sDoc, 'weeWebDocument', LIBXML_DTDLOAD);
		fire($oXML === false, 'BadXMLException');

		$oXML->registerXPathNamespace($oXML->getName(), $sXMLNS);

		return $oXML;
	}
}

?>
