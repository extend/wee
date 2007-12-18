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
	Password input widget.
*/

class weeFormPasswordBox extends weeFormTextBox
{
	/**
		Type of the input.
		Can be either text or password.
	*/

	protected $sTextType = 'password';

	/**
		Check if the SimpleXML object is valid for this widget.
		Only used in the constructor.

		@param	$oXML	The SimpleXML object.
		@return	bool	Whether the SimpleXML object is valid.
	*/

	protected function isValidXML($oXML)
	{
		return parent::isValidXML($oXML) && !isset($oXML->value);
	}

	/**
		Not used.

		@param $sNewValue The new value.
	*/

	public function setValue($sValue)
	{
		burn('BadMethodCallException', 'It is not possible to set the value of a weeFormPasswordBox widget.');
	}
}

?>
