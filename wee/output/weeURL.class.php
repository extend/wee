<?php

/*
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

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
	Generate well-formed URLs.

	Depending on the context it will create a normal or an encoded form of the URL.
	Outside a template the URL will have a normal form, inside it will be encoded.

	You must not create a new weeURL object directly inside the template. You can
	use the method $this->url for that purpose. It will make sure the object knows
	the context it is used in.
*/

class weeURL extends weeDataSource implements Printable
{
	/**
		The data to be appended to the resulting URL.
	*/

	protected $aData;

	/**
		The base of the resulting URL.
	*/

	protected $sBaseURL;

	/**
		Create the weeURL object with base data and an optional base URL.

		Usually you want to use weeURL to define common data for many URLs in a template.
		So the base URL is optional and can be defined later.

		@param $sBaseURL The base of the resulting URL.
		@param $aData Base data.
	*/

	public function __construct($sBaseURL = null, $aData = array())
	{
		$this->setURL($sBaseURL);
		$this->aData = $aData;
	}

	/**
		Add new data to be appended to the resulting URL.

		@param $aNewData Data to be added.
		@return $this
	*/

	public function addData($aNewData)
	{
		$oEncoder = $this->getEncoder();

		if ($oEncoder !== null)
			foreach ($aNewData as $sName => $sValue)
				$aNewData[$sName] = $oEncoder->decode($sValue);

		$this->aData = $aNewData + $this->aData;
		return $this;
	}

	/**
		Define the base of the resulting URL.

		It must not contain any parameter. It can contain hashes though.
		An example of base URL would be "test.php#hello" or "http://example.org/baseurl", for example.

		@param $sNewBaseURL The new base of the resulting URL.
		@return $this
	*/

	public function setURL($sNewBaseURL)
	{
		strpos($sNewBaseURL, '?') === false or burn('InvalidArgumentException',
			_WT('The base URL must not contain any parameter. Only hashes are allowed.'));

		$this->sBaseURL = $sNewBaseURL;
		return $this;
	}

	/**
		Convert the URL and data to string.

		When called from a template, the URL will be encoded.
		Otherwise it won't, although the data will be urlencoded correctly.

		You can safely use weeURL to generate URLs from anywhere as long
		as you never create a weeURL object directly from a template. For
		this purpose you must use the method weeTemplate::url.

		@return string The resulting URL.
	*/

	public function toString()
	{
		$oEncoder = $this->getEncoder();

		if (empty($this->aData)) {
			if ($oEncoder === null || $this->sBaseURL === null)
				return $this->sBaseURL;
			return $oEncoder->encode($this->sBaseURL);
		}

		$aURL = explode('#', $this->sBaseURL, 2);
		$aURL[0] .= '?';

		foreach ($this->aData as $sName => $sValue) {
			if ($sValue instanceof Printable)
				$sValue = $sValue->toString();
			$aURL[0] .= $sName . '=' . urlencode($sValue) . '&';
		}
		$aURL[0] = substr($aURL[0], 0, -1);

		if (count($aURL) > 1)
			$aURL[0] .= '#' . $aURL[1];

		if ($oEncoder !== null)
			return $oEncoder->encode($aURL[0]);
		return $aURL[0];
	}
}
