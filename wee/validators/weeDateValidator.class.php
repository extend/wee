<?php

/*
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
	A date validator.

	The input to validate must be a date as specified in the SQL standard, e.g. 1987-29-10 for october 29th, 1987.

	This validator accepts the following arguments:
	 * max:				The upper bound of the range of the valid dates.
	 * max_error:		The error message used if the date is after the date specified in the `max` argument.
	 * min:				The lower bound of the range of the valid dates.
	 * min_error:		The error message used if the date is before the date specified in the `min` argument.
	 * invalid_error:	The error message used if the input is not a date.

	 `max` and `min` arguments both accept 'current' as a special value, this special value represents
	 the current date at the time of the validation.
*/

class weeDateValidator extends weeValidator
{
	/**
		The default error messages.
	*/

	protected $aErrors = array(
		'max'		=> 'Input must be a date before %max%.',
		'min'		=> 'Input must be a date after %min%.',
		'invalid'	=> 'Input must be a date.',
	);

	/**
		Initialises a new date validator.

		@param	$aArgs						The configuration arguments of the validator.
		@throw	DomainException				The `max` argument is invalid.
		@throw	DomainException				The `min` argument is invalid.
		@throw	InvalidArgumentException	The `min` and `max` arguments do not form a valid date range.
	*/

	public function __construct(array $aArgs = array())
	{
		!isset($aArgs['min']) or is_string($aArgs['min']) and ($aArgs['min'] == 'current' or $this->isValidInput($aArgs['min']))
			or burn('DomainException',
				_WT('The `min` argument is invalid.'));

		!isset($aArgs['max']) or is_string($aArgs['max']) and ($aArgs['max'] == 'current' or $this->isValidInput($aArgs['max']))
			or burn('DomainException',
				_WT('The `max` argument is invalid.'));

		if (isset($aArgs['min'], $aArgs['max']))
		{
			if ($aArgs['min'] == 'current' or $aArgs['max'] == 'current')
				$sToday = date('Y-m-d');

			$sMin = $aArgs['min'] == 'current' ? $sToday : $aArgs['min'];
			$sMax = $aArgs['max'] == 'current' ? $sToday : $aArgs['max'];

			$sMin < $sMax
				or burn('InvalidArgumentException',
					_WT('The `min` and `max` arguments do not form a valid date range.'));
		}

		parent::__construct($aArgs);
	}

	/**
		Returns whether a given input is a valid date.

		@param	$sInput						The input.
		@return	bool						Whether the given input is a valid date.
	*/

	protected function isValidInput($sInput)
	{
		if (strlen($sInput) != 10 || $sInput[4] != '-' || $sInput[7] != '-')
			return false;

		$aDate = array(
			substr($sInput, 0, 4),	// year
			substr($sInput, 5, 2),	// month
			substr($sInput, 8, 2) 	// day
		);

		return checkdate($aDate[1], $aDate[2], $aDate[0]);
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
		Validates a date.
	*/

	protected function validate()
	{
		if (!$this->isValidInput($this->mValue))
			return $this->setError('invalid');

		if (array_value($this->aArgs, 'max', array_value($this->aArgs, 'min')) == 'current')
			$sToday = date('Y-m-d');

		if (isset($this->aArgs['min']))
		{
			$sMin = $this->aArgs['min'] == 'current' ? $sToday : $this->aArgs['min'];
			if ($this->mValue < $sMin)
				return $this->setError('min');
		}

		if (isset($this->aArgs['max']))
		{
			$sMax = $this->aArgs['max'] == 'current' ? $sToday : $this->aArgs['max'];
			if ($this->mValue > $sMax)
				return $this->setError('max');
		}
	}
}
