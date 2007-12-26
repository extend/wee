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
	Base class for selectable widgets with multiple selection possible.
*/

abstract class weeFormMultipleSelectable extends weeFormSelectable
{
	/**
		Array of selected options.
	*/

	protected $aSelection;

	/**
		Return whether the given value is selected.

		@param	$sValue	The value to check.
		@return	bool	Whether the value is selected.
	*/

	public function isSelected($sValue)
	{
		return !empty($this->aSelection[(string)$sValue]);
	}

	/**
		Select the given value.

		@param $sValue The value to select.
	*/

	public function select($sValue, $bState = true)
	{
		$this->aSelection[$sValue] = $bState;
	}

	/**
		Transform the value posted if needed.

		@param	$aData	[IN,OUT] The data sent using the form. Usually $_POST or $_GET.
	*/

	public function transformValue(&$aData)
	{
		if (isset($aData[(string)$this->oXML->name]))
			$a = array_flip($aData[(string)$this->oXML->name]);

		$aData[(string)$this->oXML->name] = array();

		$aOptions = $this->oXML->options->xpath('.//item');
		foreach ($aOptions as $aOption)
			$aData[(string)$this->oXML->name][(string)$aOption['value']] = (int)isset($a[(string)$aOption['value']]);
	}
}

?>
