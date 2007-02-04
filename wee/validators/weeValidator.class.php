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
	Base interface for validation mechanisms.
*/

interface weeValidator
{
	/**
		Initialize the validator.

		@param $mValue	The value to check.
		@param $aArgs	Configuration arguments for the validator.
	*/

	public function __construct($mValue, array $aArgs = array());

	/**
		Returns the validation error string.
		Do not call it if the validation was positive.

		@return string The error message.
	*/

	public function getError();

	/**
		Tests if the validator failed.

		@return bool True if the validation failed, false otherwise.
	*/

	public function hasError();

	/**
		Convenience function for quick validation tests.

		@param	$mValue	The value to check.
		@param	$aArgs	Configuration arguments for the validator.
		@return	bool	True if the validation SUCCEEDED, false otherwise.
		@warning		The result of this method is the inverse of hasError.
	*/

	public static function test($mValue, array $aArgs = array());
}

?>
