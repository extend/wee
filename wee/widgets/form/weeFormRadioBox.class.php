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
	Radiobox form widget.
*/

class weeFormRadioBox extends weeFormOneSelectable
{
	/**
		Index of the option displayed.
		Used to create an unique id for the radio items, since XHTML elements' id must be uniques.
	*/

	static protected $iOptionNumber = 0;

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function __toString()
	{
		//TODO:must not fire in __toString
		fire(empty($this->oXML->options), 'IllegalStateException');

		$sClass		= 'radiobox';
		if (!empty($this->oXML->class))
			$sClass	= $this->oXML->class;

		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));
		$sName		= weeOutput::encodeValue($this->oXML->name);

		if (is_null($this->sSelection))
		{
			//TODO:improve speed, make it works
			$aItems = $this->oXML->options->xpath('.//item');
			$this->select($aItems[0]['value']);
		}

		$sOptions	= null;
		foreach ($this->oXML->options->children() as $oItem)
			$sOptions .= $this->optionToString($sName, $sId, $oItem);

		return '<fieldset class="' . $sClass . '" id="' . $sId . '"><legend>' . $sLabel . '</legend><ol>' . $sOptions . '</ol></fieldset>';
	}

	/**
		Return the XHTML for a radio item of the radiobox.

		@param	$sName		Name of the radio item.
		@param	$sId		Id of the radio item.
		@param	$oItem		Selectable option's details.
		@return	string		XHTML for this radio item.
	*/

	protected function optionToString($sName, $sId, $oItem)
	{
		//TODO:check the name in item,group

		weeFormRadioBox::$iOptionNumber++;

		$sDisabled		= null;
		if ($oItem['disabled'])
			$sDisabled	= ' disabled="disabled"';

		$sHelp			= null;
		if (!empty($oItem['help']))
			$sHelp		= ' title="' . weeOutput::encodeValue(_($oItem['help'])) . '"';

		$sId			= $sId . '_' . weeFormRadioBox::$iOptionNumber;
		$sLabel			= weeOutput::encodeValue(_($oItem['label']));

		if ($oItem->getName() == 'group')
		{
			$sOptions	= null;
			foreach ($oItem->children() as $oSubItem)
				$sOptions .= $this->optionToString($sName, $sId, $oSubItem);

			return '<li class="group"><label>' . $sLabel . '</label><ol>' . $sOptions . '</ol></li>';
		}

		// else it is an item

		$sSelected		= null;
		if ($this->isSelected($oItem['value']))
			$sSelected	= ' checked="checked"';

		$sValue			= weeOutput::encodeValue($oItem['value']);

		return	'<li><label for="' . $sId . '"' . $sHelp . '><input type="radio" id="' . $sId . '" name="' . $sName .
				'" value="' . $sValue . '"' . $sDisabled . $sHelp . $sSelected . '/> ' . $sLabel . '</label></li>';
	}
}

?>
