<?php

/**
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
	A dummy Printable class to use in tests.
*/

class weeDummyPrintable implements Printable
{
	/**
		The content of the dummy printable instance.
	*/

	protected $sString;

	/**
		Construct a new dummy printable instance.

		@param $sString The content of the dummy printable instance.
	*/

	public function __construct($sString)
	{
		$this->sString = $sString;
	}

	/**
		Return the content of the dummy printable instance.

		@return string The content of the dummy printable instance.
	*/

	public function toString()
	{
		return $this->sString;
	}
}
