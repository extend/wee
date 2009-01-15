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
	Base class for validation mechanisms.
*/

abstract class weeValidator implements Serializable
{
	/**
		The error message of the validator.
	*/

	protected $sError;

	/**
		Default error messages of the validator.
	*/

	protected $aErrors = array(
		'invalid' => 'Input is invalid.'
	);

	/**
		The arguments of the validator.
	*/

	protected $aArgs = array();

	/**
		Whether a value has been attached to the validator.
	*/

	protected $bHasValue = false;

	/**
		Whether the value has been validated.
	*/

	protected $bValidated;

	/**
		The value to validate.
	*/

	protected $mValue;

	/**
		Initializes a new validator.

		@param	$aArgs						The configuration arguments of the validator.
	*/

	public function __construct(array $aArgs = array())
	{
		$this->aArgs = $aArgs + $this->aArgs;
	}

	/**
		Returns the arguments of the validator.

		@return	array						The arguments which were given to the validator.
	*/

	public function getArgs()
	{
		return $this->aArgs;
	}

	/**
		Returns the validation error message.

		@return	string						The error message.
		@throw	IllegalStateException		The validator does not have an error message because the validation succeeded.
	*/

	public function getError()
	{
		$this->hasError()
			or burn('IllegalStateException',
				_WT('The validator does not have an error message because the validation succeeded.'));

		return $this->sError;
	}

	/**
		Returns whether the validation failed.

		@return	bool						Whether the validation failed.
		@throw	IllegalStateException		No value has been attached to the validator yet.
	*/

	public function hasError()
	{
		$this->bHasValue
			or burn('IllegalStateException',
				_('No value has been attached to the validator yet.'));

		if (!$this->bValidated)
		{
			$this->validate();
			$this->bValidated = true;
		}

		return $this->sError !== null;
	}

	/**
		Returns whether the given input is valid for the validator.

		Tests performed by this method shall not depend on optional arguments which have been passed
		to the validator at construction time.

		@param	$mInput						The input.
		@return	bool						true if the input is valid, false otherwise.
	*/

	protected abstract function isValidInput($mInput);

	/**
		Serializes the validator.

		@return	string						The serialized arguments of the validator.
		@see								http://www.php.net/~helly/php/ext/spl/interfaceSerializable.html
	*/

	public function serialize()
	{
		return serialize($this->aArgs);
	}

	/**
		Formats and saves the error message.

		For a given type "x", this method will first check if the validator has an argument "x_error".
		If not, it will use the default error message provided by the aErrors property.

		If the validator does not provide a default error message for the given error type,
		a DomainException is thrown.

		This method also allows the error messages to contain references to the validator arguments:
		If the validator has an argument "x", any occurrence of "%x%" in the error message will be replaced
		by the value of the argument.

		@param	$sType						The error type.
		@throw	DomainException				The error type is invalid.
	*/

	protected function setError($sType)
	{
		$sMsg = $sType . '_error';
		if (!empty($this->aArgs[$sMsg]))	$this->sError = _T($this->aArgs[$sMsg]);
		else								$this->sError = _T($this->aErrors[$sType]);

		if (isset($this->aArgs[$sType]))
			$this->sError = str_replace('%' . $sType . '%', $this->aArgs[$sType], $this->sError);
	}

	/**
		Attachs a value to the validator.

		@param	$mValue						The value to attach.
		@return	$this						Used to chain methods.
	*/

	public function setValue($mValue)
	{
		$this->sError		= null;
		$this->bHasValue	= true;
		$this->bValidated	= false;
		$this->mValue		= $mValue;

		return $this;
	}

	/**
		Unserializes a validator.

		@param	$sSerialized				The serialized arguments of the validator.
	*/

	public function unserialize($sSerialized)
	{
		$this->__construct(unserialize($sSerialized));
	}

	/**
		Validates the given value.

		The default implementation sets the error to 'invalid' if the input is invalid
	   	accordingly to the isValidInput method.
	*/

	protected function validate()
	{
		if (!$this->isValidInput($this->mValue))
			$this->setError('invalid');
	}
}
