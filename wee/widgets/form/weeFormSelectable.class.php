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

abstract class weeFormSelectable extends weeFormWidget
{
	protected $aOptions = array();

	public function __construct($oXML)
	{
		parent::__construct($oXML);
		$this->loadOptionsFromXML($oXML);
	}

	public function addOption($sValue, $sLabel, $sHelp = null, $bDisabled = false, $bSelected = false)
	{
		$this->aOptions[] = array('value'		=> $sValue,
								  'label'		=> $sLabel,
								  'help'		=> $sHelp,
								  'disabled'	=> $bDisabled);

		if ($bSelected)
			$this->select($sValue);
	}

	public function isInOptions($sValue)
	{
		foreach ($this->aOptions as $aOption)
			//TODO:bug when using weeOptionValidator
			if ($sValue == $aOption['value'] && !$aOption['disabled'])
				return true;
		return false;
	}

	protected function isValidXML($oXML)
	{
		return parent::isValidXML($oXML) && isset($oXML->name, $oXML->label);
	}

	protected function loadOptionsFromXML($oXML)
	{
		if (isset($oXML->options))
			foreach ($oXML->options->item as $o)
			{
				//TODO:fire if empty value/label-- in addOption?

				$sHelp		= null;
				if (!empty($o['help']))
					$sHelp	= (string)$o['help'];

				$this->addOption((string)$o['value'], (string)$o['label'], $sHelp, !empty($o['disabled']), !empty($o['selected']));
			}
	}

	abstract public function isSelected($sValue);
	abstract public function select($sValue);
}

?>
