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
	Email templates handling.
	Load, configure and display templates, while also retrieving headers
	defined in the template.
*/

class weeEmailTemplate extends weeTemplate
{
	/**
		Email headers.
		The headers are available from reading after calling the
		function toString that renders the email template.

		The possible headers include all the properties defined by
		weeSendMail. This means that to fully define the From header,
		for example, you must set From and FromName.
	*/

	protected $aHeaders = array();

	/**
		Return the array of headers defined from the template.

		@return array The headers defined from the template.
	*/

	public function getHeaders()
	{
		return $this->aHeaders;
	}

	/**
		Define an additional header for this email.
		This method can be called directly from the template itself.

		@param $sName The header name.
		@param $sValue New value for that header.
	*/

	public function header($sName, $sValue)
	{
		empty($sName) and burn('UnexpectedValueException',
			_WT('The argument $sName must not be empty.'));
		empty($sValue) and burn('UnexpectedValueException',
			_WT('The argument $sValue must not be empty.'));

		(strpos($sName, "\r") !== false || strpos($sName, "\n") !== false ||
		strpos($sValue, "\r") !== false || strpos($sValue, "\n") !== false) and burn('UnexpectedValueException',
			_WT('Line breaks are not allowed in headers to prevent HTTP Response Splitting.'));
		(strpos($sName, "\0") !== false || strpos($sValue, "\0") !== false) and burn('UnexpectedValueException',
			_WT('NUL characters are not allowed in headers.'));

		$this->aHeaders[$sName] = $sValue;
	}

	/**
		Define additional headers for this email.
		This method can be called directly from the template itself.

		@param $aHeaders The headers to define (name => value).
	*/

	public function headers($aHeaders)
	{
		foreach ($aHeaders as $sName => $sValue)
			$this->header($sName, $sValue);
	}

	/**
		Return the template as a string after extracting the headers
		from the template and making them accessible through getHeaders.

		@return string The template.
	*/

	public function toString()
	{
		$oOutput = weeOutput::select(new weeTextOutput);
		$sEmail = parent::toString();
		weeOutput::select($oOutput);

		return $sEmail;
	}
}
