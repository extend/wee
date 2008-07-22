<?php

/*
	Web:Extend
	Copyright (c) 2008 Dev:Extend

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
	Helper for filling out options, selecting items and finding out what item
	exist or is selected. Mostly useful for selectable widgets like a choice list.

	To create an instance of this class you would usually call weeForm::helper.
*/

class weeFormOptionsHelper
{
	/**
		SimpleXML element for the associated widget.
	*/

	protected $oXML;

	/**
		Create the helper and give it the SimpleXML element for the associated widget.

		@param $oXML The SimpleXML element for the widget.
	*/

	public function __construct($oXML)
	{
		$this->oXML = $oXML;
	}

	/**
		Add an option to the selectable items.

		The $aOption array can contain the following elements:
			label		string	The label to display
			value		mixed	The value, if any (no value usually means it is a group node which can contain other items)
			help		string	The help message
			disabled	string	Whether this option is disabled (any non-empty value means disabled)
			selected	string	Whether this option is selected (any non-empty value means selected)

		@param $aOption		Option's information
		@param $sDestXPath	XPath leading to the node where this option will be added
	*/

	public function addOption($aOption, $sDestXPath = null)
	{
		$this->createOption($aOption, $this->translateDestXPath($sDestXPath));
	}

	/**
		Add options to the selectable items.

		@param $aOptions	Array of options
		@param $sDestXPath	XPath leading to the node where this option will be added
		@see addOption for $aOption's details
	*/

	public function addOptions($aOptions, $sDestXPath = null)
	{
		$oDest = $this->translateDestXPath($sDestXPath);

		foreach ($aOptions as $aOption)
			$this->createOption($aOption, $oDest);
	}

	/**
		Create the option in the XML tree.

		@param $aOption	Option's information
		@param $oDest	XML node where this option will be created
		@see addOption for $aOption's details
	*/

	protected function createOption($aOption, $oDest)
	{
		$oItem = $oDest->addChild(array_value($aOption, 'name', 'item'));
		unset($aOption['name']);

		if (!empty($aOption['selected']))
			$oItem->addAttribute('selected', 'selected');
		unset($aOption['selected']);

		foreach ($aOption as $sName => $sValue)
			if (strlen($aOption[$sName]) != 0)
				$oItem->addAttribute($sName, $sValue);
	}

	/**
		Return whether the given value is in the option list.

		@param	$sValue	The value to check.
		@return	bool	Whether the value is in the option list.
	*/

	public function isInOptions($sValue)
	{
		// TODO: possible xpath injection here
		$aOptions = $this->oXML->options->xpath('//item[@value="' . $sValue . '" and not(@disabled)]');
		return sizeof($aOptions != 0);
	}

	/**
		Return whether the given value is selected.

		@param	$sValue	The value to check.
		@return	bool	Whether the value is selected.
	*/

	public function isSelected($sValue)
	{
		// TODO: possible xpath injection here
		$aOptions = $this->oXML->options->xpath('//item[@value="' . $sValue . '" and @selected]');
		return sizeof($aOptions) != 0;
	}

	/**
		Select the given value.

		@param $mValue The value or array of values to select.
	*/

	public function select($mValue)
	{
		if (!is_array($mValue))
			$mValue = array($mValue);

		foreach ($mValue as $sValue)
			$this->selectItem($sValue);
	}

	/**
		Unselect any selected value.
	*/

	public function selectNone()
	{
		$aOptions = $this->oXML->options->xpath('//item[@selected]');

		foreach ($aOptions as $oItem)
		{
			$oNode = dom_import_simplexml($oItem);
			$oNode->removeAttribute('selected');
		}
	}

	/**
		Select the given value.

		@param $sValue The value to select.
	*/

	protected function selectItem($sValue)
	{
		// TODO: possible xpath injection here
		$aOption = $this->oXML->options->xpath('//item[@value="' . $sValue . '" and not(@disabled)]');
		fire(sizeof($aOptions) != 1, 'BadXMLException',
			'The value was not found in the options or was found more than once.');

		$aOption[0]['selected'] = 'selected';
	}

	/**
		Select the given value and unselect any other selected value.

		@param $sValue The value to select.
	*/

	public function selectOne($sValue)
	{
		$this->selectNone();
		$this->selectItem($sValue);
	}
}
