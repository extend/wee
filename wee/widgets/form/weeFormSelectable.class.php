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
	Base class for selectable widgets.
*/

abstract class weeFormSelectable extends weeFormWidget
{
	/**
		Options list to choose from.
	*/

	protected $aOptions = array();

	/**
		Initialize the widget using the SimpleXML object.

		@param $oXML The SimpleXML object describing the widget.
	*/

	public function __construct($oXML)
	{
		parent::__construct($oXML);
		$this->loadOptionsFromXML($oXML);
	}

	/**
		Add an option to the list.

		@param $sValue		Option's value.
		@param $sLabel		Option's label.
		@param $sHelp		Option's help text.
		@param $bDisabled	Whether the option is disabled.
		@param $bSelected	Whether the option is selected.
	*/

	public function addOption($sValue, $sLabel, $sHelp = null, $bDisabled = false, $bSelected = false)
	{
		$this->aOptions[] = array('value'		=> $sValue,
								  'label'		=> $sLabel,
								  'help'		=> $sHelp,
								  'disabled'	=> $bDisabled);

		if ($bSelected)
			$this->select($sValue);
	}

	/**
		Add all the options from the given array.
		See addOption for the option's details.

		@param $aOptions An array of options.
	*/

	public function addOptions($aOptions)
	{
		foreach ($aOptions as $aOption)
			$this->addOption(
				array_value($aOption, 'value'),
				array_value($aOption, 'label'),
				array_value($aOption, 'help'),
				array_value($aOption, 'disabled'),
				array_value($aOption, 'selected')
			);
	}

	/**
		Return whether the given value is in the option list.

		@param	$sValue	The value to check.
		@return	bool	Whether the value is in the option list.
	*/

	public function isInOptions($sValue)
	{
		foreach ($this->aOptions as $aOption)
			if ($sValue == $aOption['value'] && !$aOption['disabled'])
				return true;
		return false;
	}

	/**
		Return whether the given value is selected.

		@param	$sValue	The value to check.
		@return	bool	Whether the value is selected.
	*/

	abstract public function isSelected($sValue);

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
		Load options from the SimpleXML object.
		Called by the constructor only.

		@param $oXML The SimpleXML object.
	*/

	protected function loadOptionsFromXML($oXML)
	{
		if (isset($oXML->options))
			foreach ($oXML->options->item as $o)
			{
				$sHelp		= null;
				if (!empty($o['help']))
					$sHelp	= (string)$o['help'];

				$this->addOption((string)$o['value'], (string)$o['label'], $sHelp, !empty($o['disabled']), !empty($o['selected']));
			}
	}

	/**
		Select the given value.

		@param $sValue The value to select.
	*/

	abstract public function select($sValue);
}

?>
