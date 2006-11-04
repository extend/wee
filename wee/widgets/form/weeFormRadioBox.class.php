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

class weeFormRadioBox extends weeFormOneSelectable
{
	protected $iOptionNumber = 0;

	public function __toString()
	{
		Fire(empty($this->aOptions), 'IllegalStateException');

		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));
		$sName		= weeOutput::encodeValue($this->oXML->name);

		if (is_null($this->sSelection))
		{
			$aFirst	= current($this->aOptions);
			$this->select($aFirst['value']);
		}

		$sOptions	= null;
		foreach ($this->aOptions as $a)
			$sOptions .= '<li>' . $this->optionToString($sName, $sId, $a) . '</li>';

		return '<fieldset class="radiobox" id="' . $sId . '"><legend>' . $sLabel . '</legend><ol>' . $sOptions . '</ol></fieldset>';
	}

	protected function optionToString($sName, $sId, $aOption)
	{
		$this->iOptionNumber++;

		$sDisabled		= null;
		if ($aOption['disabled'])
			$sDisabled	= ' disabled="disabled"';

		$sHelp			= null;
		if (!empty($aOption['help']))
			$sHelp		= ' title="' . weeOutput::encodeValue(_($aOption['help'])) . '"';

		$sSelected		= null;
		if ($this->isSelected($aOption['value']))
			$sSelected	= ' checked="checked"';

		$sId			= $sId . '_' . $this->iOptionNumber;
		$sLabel			= weeOutput::encodeValue(_($aOption['label']));
		$sValue			= weeOutput::encodeValue($aOption['value']);

		return '<label for="' . $sId . '"' . $sHelp . '><input type="radio" id="' . $sId . '" name="' . $sName .
			   '" value="' . $sValue . '"' . $sDisabled . $sHelp . $sSelected . '/> ' . $sLabel . '</label>';
	}
}

?>
