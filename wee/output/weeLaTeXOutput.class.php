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
	LaTeX output driver.
*/

class weeLaTeXOutput extends weeOutput
{
	/**
		Decode a given value.

		@param	$mValue	The value to decode.
		@return	string	The decoded value.
	*/

	public function decode($mValue)
	{
		return str_replace(
			array('\textbackslash ', '\\#', '\\$', '\\%', '\\&', '\\~', '\\_', '\\^', '\\{', '\\}'),
			array('\\', '#', '$', '%', '&', '~', '_', '^', '{', '}'),
			$mValue
		);
	}

	/**
		Encode data to be displayed.

		According to the LaTeX tutorial, the following need escaping: # $ % & ~ _ ^ \ { }

		@param	$mValue	Data to encode.
		@return	string	Data encoded.
	*/

	public function encode($mValue)
	{
		return str_replace(
			array('\\', '#', '$', '%', '&', '~', '_', '^', '{', '}'),
			array('\textbackslash ', '\\#', '\\$', '\\%', '\\&', '\\~', '\\_', '\\^', '\\{', '\\}'),
			$mValue
		);
	}
}
