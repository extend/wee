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
	Check if variable passed to the constructor is a valid string according to the arguments.
	It only check the string length and if the object is string compatible currently.

	@bug Should not accept a string containing the \0 character.
	@bug Should not accept negative or null length parameters.
	@bug Should not accept a min length parameter greater than the max parameter.
*/

class weeStringValidator implements weeValidator
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
		'len'	=> 'Input must have exactly %len% characters',
		'max'	=> 'Input must have at most %max% characters',
		'min'	=> 'Input must have at least %min% characters',
		'nas'	=> 'Input must be a string',
		'nul'	=> 'Input must not contain null characters'
	);

	/**
		Check if the variable $mValue is a string according to $aArgs arguments.

		$mValue can be of any type compatible to string.
		$aArgs can contain one of the following keys:
			- len:			String length must be equal to len.
			- len_error:	Error message used if length is different than len. %len% will be replaced by the len value.
			- max:			String length must not be greater than max.
			- max_error:	Error message used if length is greater than max. %max% will be replaced by the max value.
			- min:			String length must not be smaller than min.
			- min_error:	Error message used if length is smaller than min. %min% will be replaced by the min value.
			- nas_error:	Error message used if the value is of string type or a string compatible type.

		@param	$mValue	The value to be checked.
		@param	$aArgs	Arguments to check against.
	*/

	public function __construct($mValue, array $aArgs = array())
	{
		$this->aArgs = $aArgs;

		if (is_object($mValue))
		{
			if ($mValue instanceof Printable)
				$mValue = $mValue->toString();
			elseif (is_callable(array($mValue, '__toString')))
				$mValue = $mValue->__toString();
		}

		if (is_array($mValue) || is_object($mValue))
			$this->setError('nas');
		elseif (isset($aArgs['len']) && strlen($mValue) != $aArgs['len'])
			$this->setError('len');
		elseif (isset($aArgs['max']) && strlen($mValue) > $aArgs['max'])
			$this->setError('max');
		elseif (isset($aArgs['min']) && strlen($mValue) < $aArgs['min'])
			$this->setError('min');
		elseif (strpos($mValue, "\0") !== false)
			$this->setError('nul');
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

		@return	bool	True if value checked is NOT a valid string, false if it is valid.
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
		@return	bool	True if $mValue IS a valid string, false otherwise.
	*/

	public static function test($mValue, array $aArgs = array())
	{
		$o = new self($mValue, $aArgs);
		return !$o->hasError();
	}
}

?>
