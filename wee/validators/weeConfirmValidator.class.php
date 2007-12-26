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
	Checks if the value given match the value of the confirmation element.

	Useful to check if a password entered when registering is valid, for example.
	The user types it two times, and this validator checks if it's correctly entered.
*/

class weeConfirmValidator implements weeFormValidator
{
	/**
		Arguments passed to constructor are saved here for later use.
	*/

	protected $aArgs;

	/**
		The data to check.
	*/

	protected $aData;

	/**
		Error message is saved here by setError and can be retrieved using getError.
	*/

	protected $sError;

	/**
		True if the validation failed, false otherwise.
	*/

	protected $bHasError	= false;

	/**
		The value to check.
	*/

	protected $mValue;

	/**
		Default error messages.
	*/

	protected $aErrorList	= array(
		'invalid'	=> 'Input confirmation failed');

	/**
		Initialize the validator.

		@param $mValue	The value to check.
		@param $aArgs	Configuration arguments for the validator.
	*/

	public function __construct($mValue, array $aArgs = array())
	{
		$this->aArgs	= $aArgs;
		$this->mValue	= $mValue;
	}

	/**
		Returns the validation error string.
		Do not call it if the validation was positive.

		@return string The error message.
	*/

	public function getError()
	{
		return $this->sError;
	}

	/**
		Tests if the validator failed.

		@return bool True if the validation failed, false otherwise.
	*/

	public function hasError()
	{
		fire(empty($this->aData), 'InvalidStateException',
			'You must set the form data using weeConfirmValidator::setData before calling this method.');

		if ($this->aData[(string)$this->aArgs['with']] != $this->mValue)
			$this->setError('invalid');

		return $this->bHasError;
	}

	/**
		Sets data passed to the weeForm object.
		Usually either $_POST or $_GET.

		@param $aData The data to check.
	*/

	public function setData($aData)
	{
		$this->aData = $aData;
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

		$this->sError		= _($this->sError);
	}

	/**
		Not used.

		@param $oWidget The widget to validate.
	*/

	public function setWidget($oWidget)
	{
	}

	/**
		Convenience function for quick validation tests.

		@param	$mValue	The value to check.
		@param	$aArgs	Configuration arguments for the validator.
		@return	bool	True if the validation SUCCEEDED, false otherwise.
		@warning		The result of this method is the inverse of hasError.
	*/

	public static function test($mValue, array $aArgs = array())
	{
		$o = new self($mValue, $aArgs);
		return !$o->hasError();
	}
}

?>
