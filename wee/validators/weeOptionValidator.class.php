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
	A form option validator.

	This validator checks if the given input is specified in the widget options.

	This validator accepts the following arguments:
	 - invalid_error: The error message used if the input is not available in the options.
*/

class weeOptionValidator extends weeFormValidator
{
	/**
		Default error messages.
	*/

	protected $aErrors = array(
		'invalid' => 'Input must be available in the options.'
	);

	/**
		Returns whether the given input is a valid form option for the associated widget.

		@param	$mInput			The input.
		@return	bool			Whether the input is a valid form option.
	*/

	protected function isValidInput($mInput)
	{
		$oHelper = new weeFormOptionsHelper($this->oWidget);
		return $oHelper->isInOptions($mInput);
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

		is_string($mValue) or is_int($mValue) or is_float($mValue)
			or burn('DomainException',
				_WT('$mValue is not of a correct type.'));

		return parent::setValue($mValue);
	}
}
