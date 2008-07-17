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
	Check if variable passed is a valid option of the weeSelectable widget element.
*/

class weeOptionValidator implements weeFormValidator
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

	protected $bHasError 	= false;

	/**
		The value to check.
	*/

	protected $mValue;

	/**
		The widget to validate.
	*/

	protected $oWidget;

	/**
		Default error messages.
	*/

	protected $aErrorList = array('invalid' => 'Input must be available in the options');

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
		fire(empty($this->oWidget), 'InvalidStateException',
			'You must set the widget using weeOptionValidator::setFormData before calling this method.');

		// TODO: possible xpath injection
		$a = $this->oWidget->xpath('//item[@value="' . $this->mValue . '" and not(disabled)]');
		if (empty($a))
			$this->setError('invalid');

		return $this->bHasError;
	}

	/**
		Sets the widget and complete data passed to the weeForm object.
		Usually either $_POST or $_GET.

		@param $oWidget The widget to validate.
		@param $aData The data to check, if applicable.
	*/

	public function setFormData($oWidget, $aData)
	{
		$this->oWidget = $oWidget;
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
