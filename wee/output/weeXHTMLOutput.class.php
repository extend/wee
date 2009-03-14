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
	XHTML output driver.
*/

class weeXHTMLOutput extends weeOutput
{
	/**
		Encoding used by the encode/decode methods.
	*/

	protected $sEncoding = 'utf-8';

	/**
		Initialize the output driver. Start output buffering if requested.
	*/

	public function __construct($aParams = array())
	{
		parent::__construct($aParams);

		if (!empty($aParams['encoding']))
			$this->sEncoding = $aParams['encoding'];
	}

	/**
		Decode a given value.

		@param	$mValue	The value to decode.
		@return	string	The decoded value.
	*/

	public function decode($mValue)
	{
		return html_entity_decode($mValue, ENT_COMPAT, $this->sEncoding);
	}

	/**
		Encode data to be displayed.

		@param	$mValue	Data to encode.
		@return	string	Data encoded.
	*/

	public function encode($mValue)
	{
		return htmlentities($mValue, ENT_COMPAT, $this->sEncoding);
	}
}
