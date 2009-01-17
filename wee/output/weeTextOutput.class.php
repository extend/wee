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
	Text output driver.
*/

class weeTextOutput extends weeOutput
{
	/**
		Decodes a given value.

		In this output driver, this method always return its argument.

		@param	$mValue						The value to decode.
		@return	string						The decoded value.
		@throw	InvalidArgumentException	$mValue contain a NUL character.
	*/

	public function decode($mValue)
	{
		strpos($mValue, "\0") === false
			or burn('InvalidArgumentException',
				_WT('$mValue should not contain any NUL character.'));
		return $mValue;
	}

	/**
		Encodes data to be displayed.

		Text does not need to be encoded for text output.
		However the value given will be stripped of all its
		NUL characters, to prevent attacks based on it.

		@param	$mValue						Data to encode.
		@return	string						Data encoded.
	*/

	public function encode($mValue)
	{
		return str_replace("\0", '', $mValue);
	}

	/**
		Select weeTextOutput as default output and return the object.

		@return weeTextOutput				The weeTextOutput object selected.
	*/

	public static function select()
	{
		weeOutput::$oInstance = new self;
		return weeOutput::$oInstance;
	}
}
