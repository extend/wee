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

class weeFormChoice extends weeFormOneSelectable
{
	protected $sCurrentGroup;

	public function __toString()
	{
		Fire(empty($this->aOptions), 'IllegalStateException');

		$sHelp		= null;
		if (isset($this->oXML->help))
			$sHelp	= ' title="' . weeOutput::encodeValue(_($this->oXML->help)) . '"';

		$sOptions	= null;
		foreach ($this->aOptions as $a)
		{
			if (isset($a['options']))	$sOptions .= $this->groupToString($a);
			else						$sOptions .= $this->optionToString($a);
		}

		$sId	= $this->getId();
		$sLabel	= weeOutput::encodeValue(_($this->oXML->label));
		$sName	= weeOutput::encodeValue($this->oXML->name);

		return '<label for="' . $sId . '"' . $sHelp . '>' . $sLabel . '</label> <select id="' . $sId . '" name="' .
			$sName . '"' . $sHelp . '>' . $sOptions . '</select>';
	}

	public function addOption($sValue, $sLabel, $sHelp = null, $bDisabled = false, $bSelected = false)
	{
		if (empty($this->sCurrentGroup))
			parent::addOption($sValue, $sLabel, $sHelp, $bDisabled, $bSelected);
		else
		{
			$this->aOptions[$this->sCurrentGroup]['options'][]	= array('value'		=> $sValue,
																		'label'		=> $sLabel,
																		'help'		=> $sHelp,
																		'disabled'	=> $bDisabled);

			if ($bSelected)
				$this->select($sValue);
		}
	}

	public function closeOptionGroup()
	{
		$this->sCurrentGroup = null;
	}

	protected function groupToString($aGroup)
	{
		$sDisabled		= null;
		if ($aGroup['disabled'])
			$sDisabled	= ' disabled="disabled"';

		$sHelp			= null;
		if (!empty($aGroup['help']))
			$sHelp		= ' title="' . weeOutput::encodeValue(_($aGroup['help'])) . '"';

		$sLabel		= weeOutput::encodeValue(_($aGroup['label']));

		$sOptions	= null;
		foreach ($aGroup['options'] as $a)
			$sOptions .= $this->optionToString($a);

		return '<optgroup label="' . $sLabel . '"' . $sDisabled . $sHelp . '>' . $sOptions . '</optgroup>';
	}

	protected function loadOptionsFromXML($oXML)
	{
		if (isset($oXML->options))
			foreach ($oXML->options->children() as $o)
			{
				$sHelp		= null;
				if (!empty($o->help))
					$sHelp	= (string)$o['help'];

				if ($o->getName() == 'item')
					$this->addOption((string)$o['value'], (string)$o['label'], $sHelp, !empty($o['disabled']), !empty($o['selected']));
				else
				{
					$this->openOptionGroup((string)$o['label'], $sHelp, !empty($o['disabled']));

					foreach ($o->item as $oItem)
					{
						$sItemHelp		= null;
						if (!empty($oItem->help))
							$sItemHelp	= (string)$oItem['help'];

						$this->addOption((string)$oItem['value'], (string)$oItem['label'], $sItemHelp, !empty($oItem['disabled']), !empty($oItem['selected']));
					}

					$this->closeOptionGroup();
				}
			}
	}

	public function openOptionGroup($sLabel, $sHelp = null, $bDisabled = false)
	{
		Fire(!empty($this->sCurrentGroup), 'IllegalStateException');

		$this->sCurrentGroup		= $sLabel;
		$this->aOptions[$sLabel]	= array('label'		=> $sLabel,
											'help'		=> $sHelp,
											'disabled'	=> $bDisabled,
											'options'	=> array());
	}

	protected function optionToString($aOption)
	{
		$sDisabled		= null;
		if ($aOption['disabled'])
			$sDisabled	= ' disabled="disabled"';

		$sHelp			= null;
		if (!empty($aOption['help']))
			$sHelp		= ' title="' . weeOutput::encodeValue(_($aOption['help'])) . '"';

		$sSelected		= null;
		if ($this->isSelected($aOption['value']))
			$sSelected	= ' selected="selected"';

		$sLabel			= weeOutput::encodeValue(_($aOption['label']));
		$sValue			= weeOutput::encodeValue($aOption['value']);

		return '<option value="' . $sValue . '"' . $sDisabled . $sHelp . $sSelected . '>' . $sLabel . '</option>';
	}
}

?>
