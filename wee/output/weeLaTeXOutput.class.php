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
	XHTML output driver.
*/

class weeLaTeXOutput extends weeOutput
{
	/**
		Returns an instance of the weeLaTeXOutput singleton.

		@return weeLaTeXOutput The weeLaTeXOutput object for this process.
	*/

	final public static function instance()
	{
		if (!isset(weeOutput::$oSingleton))
		{
			$s = __CLASS__;
			weeOutput::$oSingleton = new $s;
		}

		return weeOutput::$oSingleton;
	}

	/**
		Encodes data to be displayed.

		Per latex tutorial, the following need escaping: # $ % & ~ _ ^ \ { }

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

?>
