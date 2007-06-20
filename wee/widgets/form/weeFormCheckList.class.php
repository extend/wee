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
	Widget representing a list of checkboxes.
*/

class weeFormCheckList extends weeFormMultipleSelectable
{
	/**
		Index of the option displayed.
		Used to create an unique id for the radio items, since XHTML elements' id must be uniques.
	*/

	protected $iOptionNumber = 0;

	/**
		Return the option as a XHTML string.

		@param	$sName	The widget's name attribute.
		@param	$sId	The widget's id attribute.
		@param	$oItem	The option's details.
		@return	string	The option as XHTML.
	*/

	protected function optionToString($sName, $sId, $oItem)
	{
		$this->iOptionNumber++;

		$sDisabled		= null;
		if ($oItem['disabled'])
			$sDisabled	= ' disabled="disabled"';

		$sHelp			= null;
		if (!empty($oItem['help']))
			$sHelp		= ' title="' . weeOutput::encodeValue(_($oItem['help'])) . '"';

		$sSelected		= null;
		if ($this->isSelected($oItem['value']))
			$sSelected	= ' checked="checked"';

		$sId			= $sId . '_' . $this->iOptionNumber;
		$sLabel			= weeOutput::encodeValue(_($oItem['label']));
		$sValue			= weeOutput::encodeValue($oItem['value']);

		return	'<label for="' . $sId . '"' . $sHelp . '><input type="checkbox" id="' . $sId . '" name="' . $sName .
				'" value="' . $sValue . '"' . $sDisabled . $sHelp . $sSelected . '/> ' . $sLabel . '</label>';
	}

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function toString()
	{
		fire(empty($this->oXML->options), 'IllegalStateException');

		$sClass		= 'checklist';
		if (!empty($this->oXML->class))
			$sClass	= weeOutput::encodeValue($this->oXML->class);

		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));
		$sName		= weeOutput::encodeValue($this->oXML->name) . '[]';

		$i			= 0;
		$sOptions	= null;
		foreach ($this->oXML->options->children() as $oItem)
		{
			$sOptions .= '<li';
			if ($sClass == 'scrollablechecklist' && $i++ % 2 == 0)
				$sOptions .= ' class="odd"';
			$sOptions .= '>' . $this->optionToString($sName, $sId, $oItem) . '</li>';
		}

		return '<fieldset class="' . $sClass . '" id="' . $sId . '"><legend>' . $sLabel . '</legend><ol>' . $sOptions . '</ol></fieldset>';
	}
}

?>
