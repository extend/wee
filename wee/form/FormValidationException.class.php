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
	Exception thrown when a form validation fails.
*/

class FormValidationException extends RuntimeException implements Printable
{
	/**
		Error messages for each widgets of the form.
	*/

	protected $aErrors = array();

	/**
		Add an error associated to the given widget.

		@param $sWidget The name of the widget where the error occured.
		@param $sMsg The error message.
	*/

	public function addError($sWidget, $sMsg)
	{
		$this->aErrors[$sWidget][] = $sMsg;
	}

	/**
		Return all the errors.

		@return array The errors array.
	*/

	public function getErrors()
	{
		return $this->aErrors;
	}

	/**
		Return whether any error was given.
	*/

	public function hasErrors()
	{
		return !empty($this->aErrors);
	}

	/**
		Output the errors to a linebreak separated string.
	*/

	public function toString()
	{
		$sRet = '';

		foreach ($this->aErrors as $aWidgetErrors)
			foreach ($aWidgetErrors as $sError)
				$sRet .= $sError . "\n";

		return $sRet;
	}
}
