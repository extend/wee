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

		The $mOption can be a string, a printable object or a an array.

		The valid offsets of the array are:
			* label		(string):	The label to display
			* value		(mixed):	The value, if any (no value usually means it is a group node which can contain other items)
			* help		(string):	The help message
			* disabled	(string):	Whether this option is disabled (any non-empty value means disabled)
			* selected	(string):	Whether this option is selected (any non-empty value means selected)

		If the option is a string or a printable object, it will be used as the label of the option.

		@param $mOption		Option's information
		@param $sDestXPath	XPath leading to the node where this option will be added
	*/

	public function addOption($mOption, $sDestXPath = null)
	{
		if (empty($this->oXML->options))
			$this->oXML->addChild('options');

		$this->createOption($mOption, $this->translateDestXPath($sDestXPath));
	}

	/**
		Add options to the selectable items.

		@param $aOptions	Array of options
		@param $sDestXPath	XPath leading to the node where this option will be added
		@see addOption for $mOption's details
	*/

	public function addOptions($aOptions, $sDestXPath = null)
	{
		if (empty($this->oXML->options))
			$this->oXML->addChild('options');

		$oDest = $this->translateDestXPath($sDestXPath);

		foreach ($aOptions as $mOption)
			$this->createOption($mOption, $oDest);
	}

	/**
		Create the option in the XML tree.

		@param $mOption	Option's information
		@param $oDest	XML node where this option will be created
		@see addOption for $mOption's details
	*/

	protected function createOption($mOption, $oDest)
	{
		// Convert the option from object/strings to array
		if ($mOption instanceof Printable)
			$mOption = $mOption->toString();

		if (!is_array($mOption))
			$mOption = array('label' => $mOption);

		// We'll use DOM to create our nodes
		if (!($oDest instanceof DOMNode))
			$oDest = dom_import_simplexml($oDest);

		// Retrieve the node type
		$sName = array_value($mOption, 'name', 'item');
		unset($mOption['name']);

		// Create the new node
		$oNewNode = $oDest->ownerDocument->createElement($sName);

		// First load the children nodes
		if ($sName == 'group') {
			foreach ($mOption['children'] as $mChild)
				$this->createOption($mChild, $oNewNode);
			unset($mOption['children']);
		}

		// Then the attributes
		foreach ($mOption as $sName => $sValue)
			$oNewNode->setAttribute($sName, $sValue);

		// And finally append it
		$oDest->appendChild($oNewNode);
	}

	/**
		Return whether the given value is in the option list.

		@param	$sValue	The value to check.
		@return	bool	Whether the value is in the option list.
	*/

	public function isInOptions($sValue)
	{
		$sEscapedValue	= xmlspecialchars($sValue);
		$aOptions		= $this->oXML->options->xpath(
			'.//item[(not(@value) and @label="' . $sEscapedValue . '" or @value="' . $sEscapedValue . '") and not(@disabled)]');

		if ($aOptions === false)
			return false;
		return sizeof($aOptions) != 0;
	}

	/**
		Return whether the given value is selected.

		@param	$sValue	The value to check.
		@return	bool	Whether the value is selected.
	*/

	public function isSelected($sValue)
	{
		$sEscapedValue	= xmlspecialchars($sValue);
		$aOptions		= $this->oXML->options->xpath(
			'.//item[(not(@value) and @label="' . $sEscapedValue . '" or @value="' . $sEscapedValue . '") and @selected]
		');

		if ($aOptions === false)
			return false;
		return sizeof($aOptions) != 0;
	}

	/**
		Select the given value.

		@param $mValue The value or array of values to select.
	*/

	public function select($mValue)
	{
		if ($mValue instanceof Printable)
			$mValue = $mValue->toString();

		foreach ((array)$mValue as $sValue)
			$this->selectItem($sValue);
	}

	/**
		Unselect any selected value.
	*/

	public function selectNone()
	{
		$aOptions = $this->oXML->options->xpath('.//item[@selected]');

		if ($aOptions !== false)
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
		$sEscapedValue	= xmlspecialchars($sValue);
		$aOption		= $this->oXML->options->xpath(
			'.//item[(not(@value) and @label="' . $sEscapedValue . '" or @value="' . $sEscapedValue . '") and not(@disabled)]'
		);

		($aOption === false || sizeof($aOption) != 1) and burn('BadXMLException',
			_WT('The value was not found in the options or was found more than once.'));

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
		($aDest === false || sizeof($aDest) != 1) and burn('BadXMLException',
			sprintf(_WT('The XPath statement "%s" must return exactly 1 result, %d were returned.'), $sDestXPath, sizeof($aDest)));

		return $aDest[0];
	}
}
