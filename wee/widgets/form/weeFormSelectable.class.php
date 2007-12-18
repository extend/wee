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
		Initialize the widget using the SimpleXML object.

		@param $oXML The SimpleXML object describing the widget.
	*/

	public function __construct($oXML)
	{
		parent::__construct($oXML);

		if (empty($this->oXML->options))
			$this->oXML->addChild('options');

		$aOptions = $this->oXML->options->xpath('.//item[@selected]');
		foreach ($aOptions as $aOption)
			$this->select((string)$aOption['value']);
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
		if (empty($aOption['name']))
			$aOption['name'] = 'item';

		$oItem = $oDest->addChild($aOption['name']);
		unset($aOption['name']);

		if (!empty($aOption['selected']))
			$this->select($aOption['value']);
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
		$aOptions = $this->oXML->options->xpath('.//item');

		foreach ($aOptions as $aOption)
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
		Select the given value.

		@param $sValue The value to select.
	*/

	abstract public function select($sValue);

	/**
		Replace the label.

		@param $sNewLabel The new label.
	*/

	public function setLabel($sNewLabel)
	{
		$this->oXML->label = $sNewLabel;
	}

	/**
		Return the XML node at the specified XPath.
		There must be only ONE result returned.

		@param	$sDestXPath			XPath statement
		@return	weeSimpleXMLHack	XML node found at the specified path
	*/

	protected function translateDestXPath($sDestXPath)
	{
		if (empty($sDestXPath))
			return $this->oXML->options;

		$aDest = $this->oXML->options->xpath($sDestXPath);
		fire(sizeof($aDest) != 1, 'BadXMLException',
			'The XPath statement ' . $sDestXPath . ' must return exactly 1 result, ' . sizeof($aDest) . ' were returned.');

		return $aDest[0];
	}
}

?>
