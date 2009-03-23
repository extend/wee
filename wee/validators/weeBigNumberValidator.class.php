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
	A big number validator.

	Otherwise the comparisons for the `min` and `max` parameters will be performed using
	string functions.

	This validator accepts the following arguments:
	 * format:			The format of the number to validate, can be either 'int' or 'float', defaults to 'int'.
	 * int_error:		The error message used if not a valid integer representation and the requested `format` is 'int'.
	 * max:				The upper bound of the range of the valid numbers.
	 * max_error:		The error message used if the number is greater than the `max` argument.
	 * min:				The lower bound of the range of the valid numbers.
	 * min_error:		The error message used if the number is smaller than the `min` argument.
	 * invalid_error:	The error message used if the input is not numeric.
*/

class weeBigNumberValidator extends weeValidator
{
	/**
		Default error messages.
	*/

	protected $aErrors = array(
		'int'		=> 'Input must be an integer value.',
		'max'		=> 'Input must not be greater than %max%.',
		'min'		=> 'Input must not be smaller than %min%.',
		'invalid'	=> 'Input must be a number.',
	);

	/**
		The arguments of the validator.
	*/

	protected $aArgs = array(
		'format' => 'int'
	);

	/**
		Initialises a new number validator.

		@param	$aArgs						The configuration arguments of the validator.
		@throw	DomainException				The `format` argument is invalid.
		@throw	DomainException				The `max` argument is invalid.
		@throw	DomainException				The `min` argument is invalid.
		@throw	InvalidArgumentException	The `min` and `max` arguments do not form a valid number range.
	*/

	public function __construct(array $aArgs = array())
	{
		!isset($aArgs['format']) or $aArgs['format'] == 'int' or $aArgs['format'] == 'float'
			or burn('InvalidArgumentException',
				_WT('The `format` argument must be either "int" or "float".'));

		!isset($aArgs['min']) or is_string($aArgs['min']) and $this->isValidInput($aArgs['min'])
			or burn('DomainException',
				_WT('The `min` argument is invalid.'));

		!isset($aArgs['max']) or is_string($aArgs['max']) and $this->isValidInput($aArgs['max'])
			or burn('DomainException',
				_WT('The `max` argument is invalid.'));

		if (isset($aArgs['min'], $aArgs['max']))
		{
			$this->compare($aArgs['min'], $aArgs['max']) < 0
				or burn('InvalidArgumentException',
					_WT('The `min` and `max` arguments do not form a valid number range.'));
		}

		parent::__construct($aArgs);
	}

	/**
		Compares two big numbers.

		Returns an integer negative if $mLeft is less than $mRight, positive if $mLeft is greater
		than $mRight and 0 if $mLeft and $mRight are equal.

		@param	$sLeft						The left value.
		@param	$sRight						The right value.
		@return	int							The result of the comparison.
	*/

	protected function compare($sLeft, $sRight)
	{
		$iLeftDecPoint	= strpos($sLeft, '.');
		$iRightDecPoint	= strpos($sRight, '.');
		if ($iLeftDecPoint !== false || $iRightDecPoint !== false)
		{
			$sLeftDec	= '0';
			$sRightDec	= '0';

			if ($iLeftDecPoint !== false)
			{
				$sLeftDec .= rtrim(substr($sLeft, $iLeftDecPoint + 1), '0');
				$sLeft = substr($sLeft, 0, $iLeftDecPoint);
			}

			if ($iRightDecPoint !== false)
			{
				$sRightDec .= rtrim(substr($sRight, $iRightDecPoint + 1), '0');
				$sRight = substr($sRight, 0, $iRightDecPoint);
			}
		}

		$bLeftIsNeg		= $sLeft[0] == '-';
		$bRightIsNeg	= $sRight[0] == '-';

		if ($bLeftIsNeg != $bRightIsNeg)
			return $bLeftIsNeg ? -1 : 1;

		if ($bLeftIsNeg)
		{
			$sLeft	= substr($sLeft, 1);
			$sRight	= substr($sRight, 1);
		}

		$sLeft	= ltrim($sLeft, '0');
		$sRight	= ltrim($sRight, '0');
		$iLeft	= strlen($sLeft);
		$iRight	= strlen($sRight);

		if ($iLeft != $iRight)
			return $bLeftIsNeg ? $iRight - $iLeft : $iLeft - $iRight;

		$i = $bLeftIsNeg ? strcmp($sRight, $sLeft) : strcmp($sLeft, $sRight);
		if ($i != 0 || !isset($sLeftDec))
			return $i;

		return $bLeftIsNeg ? strcmp($sRightDec, $sLeftDec) : strcmp($sLeftDec, $sRightDec);
	}

	/**
		Returns whether a given input is a valid number.

		@param	$sInput						The input.
		@return	bool						Whether the given input is a valid number.
	*/

	protected function isValidInput($sInput)
	{
		$i = strlen($sInput);
		if (!$i)
			return false;

		$bIsNeg = $sInput[0] == '-';
		if ($bIsNeg)
		{
			$sInput = substr($sInput, 1);
			if (!--$i)
				return false;
		}

		$iDecPoint = strpos($sInput, '.');
		if ($iDecPoint === false)
		{
			if ($bIsNeg && !trim($sInput, '0'))
				return false;

			return ctype_digit($sInput);
		}

		if (!$iDecPoint || $iDecPoint == $i - 1 || strpos($sInput, '.', $iDecPoint + 1))
			return false;

		if ($bIsNeg && trim($sInput, '0') == '.')
			return false;

		return ctype_digit(substr($sInput, 0, $iDecPoint)) && ctype_digit(substr($sInput, $iDecPoint + 1));
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
		Validates a number.
	*/

	protected function validate()
	{
		if (!$this->isValidInput($this->mValue))
			return $this->setError('invalid');

		if ($this->aArgs['format'] == 'int' && strpos($this->mValue, '.') !== false)
			return $this->setError('int');

		if (isset($this->aArgs['max']) && $this->compare($this->mValue, $this->aArgs['max']) > 0)
			$this->setError('max');
		elseif (isset($this->aArgs['min']) && $this->compare($this->mValue, $this->aArgs['min']) < 0)
			$this->setError('min');
	}
}
