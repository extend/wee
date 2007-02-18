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
	Base class for checkable widgets.
	Checkable widgets can be checked or not.
	There is no other state available.
*/

abstract class weeFormCheckable extends weeFormWidget
{
	/**
		Whether the widget is checked.
	*/

	protected $bChecked;

	/**
		Initialize the widget using the SimpleXML object.

		@param $oXML The SimpleXML object describing the widget.
	*/

	public function __construct($oXML)
	{
		parent::__construct($oXML);
		$this->bChecked = !empty($oXML->checked);
	}

	/**
		Check or uncheck the widget.

		@param $bState Whether the widget is checked.
	*/

	public function check($bState = true)
	{
		$this->bChecked = $bState;
	}

	/**
		Return whether the widget is checked.

		@return bool Whether the widget is checked.
	*/

	public function isChecked()
	{
		return $this->bChecked;
	}

	/**
		Check if the SimpleXML object is valid for this widget.
		Only used in the constructor.

		@param	$oXML	The SimpleXML object.
		@return	bool	Whether the SimpleXML object is valid.
	*/

	protected function isValidXML($oXML)
	{
		return parent::isValidXML($oXML) && !(empty($oXML->name) || empty($oXML->label));
	}

	/**
		Transform the value posted if needed.
		Return false if the value was not set.

		@param	$aData	[IN,OUT] The data sent using the form. Usually $_POST or $_GET.
		@return	bool	Whether the value is present.
	*/

	public function transformValue(&$aData)
	{
		if (!isset($aData[(string)$this->oXML->name]))
			$aData[(string)$this->oXML->name] = 0;
		return true;
	}
}

?>
