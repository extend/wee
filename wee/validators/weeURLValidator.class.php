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
	An URL validator.

	This validator accepts the following arguments:
	 - invalid_error: The error message used if the input is not a valid URL.
*/

class weeURLValidator extends weeValidator
{
	/**
		Default error messages.
	*/

	protected $aErrors = array(
		'invalid' => 'Input must be a valid URL.'
	);

	/**
		Returns whether the given input is a valid URL.

		@param	$sInput			The input.
		@return	bool			Whether the input is a valid URL.
	*/

	protected function isValidInput($sInput)
	{
		return filter_var($sInput, FILTER_VALIDATE_URL) !== false;
	}

	/**
		Attachs a value to the validator.

		$mValue must be either a string, an instance of Printable or an object castable to string.

		@param	$mValue						The value to attach.
		@return	$this						Used to chain methods.
		@throw	DomainException				$mValue is not of a correct type.
	*/

	public function setValue($mValue)
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

		return parent::setValue($mValue);
	}

	/**
		Convenience function for inline validating of variables.

		@param	$mValue			The value to validate.
		@param	$aArgs			The configuration arguments of the validator.
		@return	bool			Whether the variable is valid.
	*/

	public static function test($mValue, array $aArgs = array())
	{
		$o = new self($aArgs);
		return !$o->setValue($mValue)->hasError();
	}
}
