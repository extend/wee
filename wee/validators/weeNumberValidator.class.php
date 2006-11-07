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
	Check if variable passed to the constructor is a valid number according to the arguments.
	It can check if the number is an integer or a float, and if it is comprised between a min and max value.

	@bug A large integer (example: 20000000000000000) is not considered a number, unless it is passed in string type.
	@bug Scientific notation (example: '2E+16') is not considered a valid number, but int or float error is triggered, not nan.
	@warning Inconsistency: float value 1.0 is valid but not its string equivalent, '1.0'.
*/

class weeNumberValidator implements weeValidator
{
	/**
		Arguments passed to constructor are saved here for later use.
	*/

	protected $aArgs;

	/**
		Error message is saved here by setError and can be retrieved using getError.
	*/

	protected $sError;

	/**
		Default error messages.
	*/

	protected $aErrorList	= array(
		'float'	=> 'Input must be a decimal value',
		'int'	=> 'Input must be an integer value',
		'max'	=> 'Input must not be greater than %max%',
		'min'	=> 'Input must not be smaller than %min%',
		'nan'	=> 'Input must be a number',
	);

	/**
		Check if the variable $mValue is a number according to $aArgs arguments.

		$mValue can be of any type, but if it's not numeric the validation will fail with a NAN (Not A Number) error.
		$aArgs can contain one of the following keys:
			- format:		Either integer or float.
			- float_error:	Error message used if value is not decimal.
			- int_error:	Error message used if value is not integer.
			- max:			Value must not be greater than max.
			- max_error:	Error message used if value is greater than max. %max% will be replaced by the max value.
			- min:			Value must not be smaller than min.
			- min_error:	Error message used if value is smaller than min. %min% will be replaced by the min value.
			- nan_error:	Error message used if value is not numeric.

		@param	$mValue	The value to be checked.
		@param	$aArgs	Arguments to check against.
	*/

	public function __construct($mValue, array $aArgs = array())
	{
		$this->aArgs = $aArgs;

		if (!is_numeric($mValue))
			$this->setError('nan');
		elseif (isset($aArgs['max']) && $mValue > $aArgs['max'])
			$this->setError('max');
		elseif (isset($aArgs['min']) && $mValue < $aArgs['min'])
			$this->setError('min');
		else
		{
			if (empty($aArgs['format']))
				$aArgs['format'] = 'int';

			$mValue = (string)$mValue;
			if (substr($mValue, 0, 1) == '-')
				$mValue = substr($mValue, 1);

			if ($aArgs['format'] == 'float' && !ctype_digit(str_replace('.', '', $mValue)))
				$this->setError('float');
			elseif ($aArgs['format'] == 'int' && !ctype_digit($mValue))
				$this->setError('int');
		}
	}

	/**
		Get the error message, if any.

		@return	string	The error message, or null if there is no error.
	*/

	public function getError()
	{
		return $this->sError;
	}

	/**
		Get the result of the check performed in the constructor.

		@return	bool	True if value checked is NOT a valid number, false if it is valid.
	*/

	public function hasError()
	{
		return !empty($this->sError);
	}

	/**
		Format and save the error message.

		@param	$sType	The error type. Used to retrieve the error message. See the constructor documentation for details.
	*/

	protected function setError($sType)
	{
		$sMsg = $sType . '_error';
		if (!empty($this->aArgs[$sMsg]))	$this->sError = $this->aArgs[$sMsg];
		else								$this->sError = $this->aErrorList[$sType];

		if (!empty($this->aArgs[$sType]))
			$this->sError = str_replace('%' . $sType . '%', $this->aArgs[$sType], _($this->sError));
	}

	/**
		Convenience function for inline checking of variables.

		@param	$mValue	The value to be checked.
		@param	$aArgs	Arguments to check against. See the constructor documentation for details.
		@return	bool	True if $mValue IS a valid number, false otherwise.
	*/

	public static function test($mValue, array $aArgs = array())
	{
		$o = new self($mValue, $aArgs);
		return !$o->hasError();
	}
}

?>
