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
	Choice widget.

	It is the XHTML select element. Only one value selectable.
	It is possible to define groups of values.
*/

class weeFormChoice extends weeFormOneSelectable
{
	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function __toString()
	{
		//TODO:must not fire in __toString
		fire(empty($this->oXML->options), 'IllegalStateException');

		$sClass		= null;
		if (!empty($this->oXML->class))
			$sClass	= ' class="' . weeOutput::encodeValue($this->oXML->class) . '"';

		$sHelp		= null;
		if (isset($this->oXML->help))
			$sHelp	= ' title="' . weeOutput::encodeValue(_($this->oXML->help)) . '"';

		$sOptions	= null;
		foreach ($this->oXML->options->children() as $oItem)
			$sOptions .= $this->optionToString($oItem);

		$sId	= $this->getId();
		$sLabel	= weeOutput::encodeValue(_($this->oXML->label));
		$sName	= weeOutput::encodeValue($this->oXML->name);

		return '<label for="' . $sId . '"' . $sHelp . '>' . $sLabel . '</label> <select id="' . $sId . '" name="' .
			$sName . '"' . $sClass . $sHelp . '>' . $sOptions . '</select>';
	}

	/**
		Add two levels of options from a SQL database.

		@param	$oDatabase	Database to query from
		@param	$sTable		Table to query from
		@param	$sValue		Name of the value column
		@param	$sLabel		Name of the label column
		@param	$sParent	Name of the parent id column
	*/

	public function addOptionGroups($oDatabase, $sTable, $sValue, $sLabel, $sParent)
	{
		//TODO:check args

		$oItems = $oDatabase->query('
			SELECT ' . $sValue . ' AS value, ' . $sLabel . ' AS label
				FROM ' . $sTable . ' AS a
				WHERE ' . $sParent . ' IS NULL
				ORDER BY (SELECT COUNT(*) FROM ' . $sTable . ' AS b WHERE a.' . $sValue . '=b.' . $sParent . ' LIMIT 1)=0 DESC, label
		');

		foreach ($oItems as $aItem)
		{
			$oSub = $oDatabase->query('
				SELECT ' . $sValue . ' AS value, ' . $sLabel . ' AS label
					FROM ' . $sTable . '
					WHERE ' . $sParent . '=?
					ORDER BY label
			', $aItem['value']);

			if ($oSub->numResults() == 0)
				$this->addOption($aItem);
			else
			{
				unset($aItem['value']);
				$this->addOption($aItem + array('name' => 'group'));
				$this->addOptions($oSub, ".//group[@label='" . $aItem['label'] . "']");
			}
		}
	}

	/**
		Return the option as a XHTML string.

		@param	$oItem	The option's item.
		@return	string	The option as XHTML.
	*/

	protected function optionToString($oItem)
	{
		//TODO:check the name in item,group

		$sDisabled		= null;
		if ($oItem['disabled'])
			$sDisabled	= ' disabled="disabled"';

		$sHelp			= null;
		if (!empty($oItem['help']))
			$sHelp		= ' title="' . weeOutput::encodeValue(_($oItem['help'])) . '"';

		$sLabel			= weeOutput::encodeValue(_($oItem['label']));

		if ($oItem->getName() == 'group')
		{
			$sOptions	= null;
			foreach ($oItem->children() as $oSubItem)
			{
				fire($oSubItem->getName() != 'item', 'BadXMLException'); // only two levels of items
				$sOptions .= $this->optionToString($oSubItem);
			}

			return '<optgroup label="' . $sLabel . '"' . $sDisabled . $sHelp . '>' . $sOptions . '</optgroup>';
		}

		// else it is an item

		$sClass			= null;
		if (!empty($oItem['class']))
			$sClass		= ' class="' . $oItem['class'] . '"';

		$sSelected		= null;
		if ($this->isSelected($oItem['value']))
			$sSelected	= ' selected="selected"';

		$sValue			= weeOutput::encodeValue($oItem['value']);

		return '<option value="' . $sValue . '"' . $sClass . $sDisabled . $sHelp . $sSelected . '>' . $sLabel . '</option>';
	}
}

?>
