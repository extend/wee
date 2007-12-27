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
	Base class for writable form widgets.
*/

abstract class weeFormWritable extends weeFormWidget
{
	/**
		Value of this widget.
	*/

	protected $sValue;

	/**
		Initialize the widget using the SimpleXML object.

		@param $oXML The SimpleXML object describing the widget.
	*/

	public function __construct($oXML)
	{
		parent::__construct($oXML);

		if (isset($oXML->value))
			$this->setValue($oXML->value);
	}

	/**
		Return the current value.

		@return string The current value.
	*/

	public function getValue()
	{
		return $this->sValue;
	}

	/**
		Check if the SimpleXML object is valid for this widget.
		Only used in the constructor.

		@param	$oXML	The SimpleXML object.
		@return	bool	Whether the SimpleXML object is valid.
	*/

	protected function isValidXML($oXML)
	{
		return parent::isValidXML($oXML) && isset($oXML->name, $oXML->label);
	}

	/**
		Replace the label.

		@param $sNewLabel The new label.
	*/

	public function setLabel($sNewLabel)
	{
		$this->oXML->label = $sNewLabel;
	}

	/**
		Set a new value.

		@param $sNewValue The new value.
	*/

	public function setValue($sNewValue)
	{
		$this->sValue = $sNewValue;
	}
}

?>
