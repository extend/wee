<?php

/**
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
	An XML encoder.
*/

class weeXMLEncoder extends weeEncoder
{
	/**
		Decode a given value.

		@param	$sValue	The value to decode.
		@return	mixed	The decoded value.
	*/

	public function decode($sValue)
	{
		return xmlspecialchars_decode($sValue);
	}

	/**
		Encode a given value.

		@param	$mValue	The value to encode.
		@return	string	The encoded value.
	*/

	public function encode($mValue)
	{
		return xmlspecialchars($mValue);
	}

	/**
		Return the MIME type of the format which uses this encoding.

		@return string The MIME type of the format which uses this encoding.
	*/

	public function getMIMEType()
	{
		return 'application/xml';
	}
}
