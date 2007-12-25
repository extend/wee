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
	Checks if the given date is valid according to the arguments.
*/

class weeTimeValidator implements weeValidator
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
		True if the validation failed, false otherwise.
	*/

	protected $bHasError	= false;

	/**
		Default error messages.
	*/

	protected $aErrorList	= array(
		'len'	=> 'Input must have exactly 5 characters',
		'nat'	=> 'Input must be a time',
	);

	/**
		Check if the variable $mValue is a time according to $aArgs arguments.

		$mValue can be of any type compatible to string.
		$aArgs can contain one of the following keys:
			- invalid_error:	Error message used if the time given is not valid.

		@param	$mValue	The value to be checked.
		@param	$aArgs	Arguments to check against.
	*/

	public function __construct($mValue, array $aArgs = array())
	{
		$this->aArgs = $aArgs;
		
		if (strlen($mValue) != 5)
			$this->setError('len');
		elseif (strtotime($mValue) == false)
			$this->setError('nat');
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

		@return	bool	True if value checked is NOT a valid email, false if it is valid.
	*/

	public function hasError()
	{
		return $this->bHasError;
	}

	/**
		Format and save the error message.

		@param	$sType	The error type. Used to retrieve the error message. See the constructor documentation for details.
	*/

	protected function setError($sType)
	{
		$this->bHasError	= true;

		$sMsg = $sType . '_error';
		if (!empty($this->aArgs[$sMsg]))	$this->sError = $this->aArgs[$sMsg];
		else								$this->sError = $this->aErrorList[$sType];
	}

	/**
		Convenience function for inline checking of variables.

		@param	$mValue	The value to be checked.
		@param	$aArgs	Arguments to check against. See the constructor documentation for details.
		@return	bool	True if $mValue IS a valid email, false otherwise.
	*/

	public static function test($mValue, array $aArgs = array())
	{
		$o = new self($mValue, $aArgs);
		return !$o->hasError();
	}
}

?>