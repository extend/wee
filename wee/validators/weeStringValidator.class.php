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
	A string validator.

	This validator accepts the following arguments:
	 - invalid_error:	The error message used if the input is not of a string compatible type.
	 - len:				The length that the string must have.
	 - len_error:		The error message used if the string has a length not equal to the `len` argument.
	 - max:				The maximal length that the string must have.
	 - max_error:		The error message used if the string has a length greater than the `max` argument.
	 - min:				The minimal length that the string must have.
	 - min_error:		The error message used if the string has a length smaller than the `min` argument.
	 - nul_error:		The error message used if the string contains null characters.
*/

class weeStringValidator extends weeValidator
{
	/**
		Default error messages.
	*/

	protected $aErrors = array(
		'len'		=> 'Input must have exactly %len% characters.',
		'max'		=> 'Input must have at most %max% characters.',
		'min'		=> 'Input must have at least %min% characters.',
		'invalid'	=> 'Input must be a string.',
		'nul'		=> 'Input must not contain null characters.'
	);

	/**
		Initialises a string validator.

		@param	$aArgs						The configuration arguments of the validator.
		@throw	DomainException				The `len` argument is invalid.
		@throw	DomainException				The `min` argument is invalid.
		@throw	DomainException				The `max` argument is invalid.
		@throw	InvalidArgumentException	The `min` and `max` arguments do not form a valid length range.
		@throw	InvalidArgumentException	The `len` and one of the `min` or `max` arguments are both specified.
	*/

	public function __construct(array $aArgs = array())
	{
		!isset($aArgs['len']) or filter_var($aArgs['len'], FILTER_VALIDATE_INT) !== false and $aArgs['len'] >= 0
			or burn('DomainException',
				_WT('The `len` argument is invalid.'));

		!isset($aArgs['min']) or filter_var($aArgs['min'], FILTER_VALIDATE_INT) !== false and $aArgs['min'] >= 0
			or burn('DomainException',
				_WT('The `min` argument is invalid.'));

		!isset($aArgs['max']) or filter_var($aArgs['max'], FILTER_VALIDATE_INT) !== false and $aArgs['max'] >= 0
			or burn('DomainException',
				_WT('The `max` argument is invalid.'));

		if (isset($aArgs['min'], $aArgs['max']))
		{
			(int)$aArgs['min'] < $aArgs['max']
				or burn('InvalidArgumentException',
					_WT('The `min` and `max` arguments do not form a valid length range.'));
		}

		!isset($aArgs['len']) or !isset($aArgs['min']) and !isset($aArgs['max'])
			or burn('InvalidArgumentException',
				_WT('The `len` and one of the `min` or `max` arguments are both specified.'));

		parent::__construct($aArgs);
	}

	/**
		Returns whether if the given input is a valid string or of a compatible type.

		@param	$sInput						The input.
		@return	bool						Whether the given input is a valid string or of a compatible type.
	*/

	protected function isValidInput($mInput)
	{
		return is_scalar($mInput) || $mInput == null;
	}

	/**
		Convenience function for inline validating of variables.

		@param	$mValue						The value to validate.
		@param	$aArgs						The configuration arguments of the validator.
		@return	bool						Whether the variable is valid.
	*/

	public static function test($mValue, array $aArgs = array())
	{
		$o = new self($aArgs);
		return !$o->setValue($mValue)->hasError();
	}

	/**
		Attachs a value to the validator.

		$mValue must be either a string, an integer, a float, an instance of Printable or an object castable to string.

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

		is_scalar($mValue) or is_null($mValue) or is_array($mValue)
			or burn('DomainException',
				_WT('$mValue is not of a correct type.'));

		return parent::setValue($mValue);
	}

	/**
		Validates a string.
	*/

	protected function validate()
	{
		if (!$this->isValidInput($this->mValue))
			return $this->setError('invalid');

		if (strpos($this->mValue, "\0") !== false)
			return $this->setError('nul');

		$iLength = strlen($this->mValue);

		if (isset($this->aArgs['len']) && $iLength != $this->aArgs['len'])
			$this->setError('len');
		elseif (isset($this->aArgs['max']) && $iLength > $this->aArgs['max'])
			$this->setError('max');
		elseif (isset($this->aArgs['min']) && $iLength < $this->aArgs['min'])
			$this->setError('min');
	}
}
