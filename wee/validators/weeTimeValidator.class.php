<?php

/*
	Web:Extend
	Copyright (c) 2006, 2008 Dev:Extend

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
	A time validator.

	The input must be a time in format HH:mm, as in 00:00 for midnight.

	This validator accepts the following parameters:
	 - invalid_error: The error message used if the input is not a valid time.
*/

class weeTimeValidator extends weeValidator
{
	/**
		Default error messages.
	*/

	protected $aErrors = array(
		'invalid' => 'Input must be a time.',
	);

	/**
		Initialises a new date validator.

		$mValue must be either a string, an instance of Printable or an object castable to string.

		@param	$mValue			The value to validate.
		@param	$aArgs			The configuration arguments of the validator.
		@throw	DomainException	$mValue is not of a correct type.
	*/

	public function __construct($mValue, array $aArgs = array())
	{
		if (is_object($mValue))
		{
			if ($mValue instanceof Printable)
				$mValue = $mValue->toString();
			elseif (method_exists($mValue, '__toString'))
				$mValue = (string)$mValue;
		}

		is_string($mValue)
			or burn('DomainException',
				_WT('$mValue is not of a correct type.'));

		parent::__construct($mValue, $aArgs);
	}

	/**
		Returns whether a given input is a valid time.

		@param	$sInput			The input.
		@return	bool			Whether the given input is a valid time.
	*/

	protected function isValidInput($sInput)
	{
		return strlen($sInput) != 5 || $sInput[2] != ':' || mktime(substr($sInput, 0, 2), substr($sInput, 3, 2)) !== false;
	}

	/**
		Convenience function for inline validating of variables.

		@param	$mValue			The value to validate.
		@param	$aArgs			The configuration arguments of the validator.
		@return	bool			Whether the variable is valid.
	*/

	public static function test($mValue, array $aArgs = array())
	{
		$o = new self($mValue, $aArgs);
		return !$o->hasError();
	}
}
