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
	A form confirmation validator.

	This validator checks if the value given match the value of the confirmation element.

	Useful to check if a password entered when registering is valid, for example.
	The user types it two times, and this validator checks if it's correctly entered.

	This validator accepts the following arguments:
	 - invalid_error:	The error message used if the input is not confirmed in the form data.
	 - with:			The name of the widget which value must be confirmed by the validator (mandatory).
*/

class weeConfirmValidator extends weeFormValidator
{
	/**
		Default error messages.
	*/

	protected $aErrors = array(
		'invalid' => 'Input confirmation failed.'
	);
 
 	/**
		Initialises a new option validator.

		$mValue must be either a scalar, an instance of Printable or an object castable to string.

		@param	$aArgs						The configuration arguments of the validator.
		@throw	InvalidArgumentException	The argument `with` is missing.
	*/

	public function __construct(array $aArgs = array())
	{
		!empty($aArgs['with'])
			or burn('InvalidArgumentException',
				_WT('The argument `with` is mandatory.'));

		parent::__construct($aArgs);
	}

	/**
		Returns whether the given value is confirmed in the form data.

		@param	$mInput						The input.
		@return	bool						Whether the given value is confirmed.
	*/

	protected function isValidInput($mInput)
	{
		return array_value($this->aData, $this->aArgs['with']) == $mInput;
	}

	/**
		Attaches a value to the validator.

		$mValue must be either a scalar, an instance of Printable or an object castable to string.

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

		is_scalar($mValue)
			or burn('DomainException',
				_WT('$mValue is not of a correct type.'));

		return parent::setValue($mValue);
	}
}
