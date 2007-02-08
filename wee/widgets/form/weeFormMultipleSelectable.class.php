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

abstract class weeFormMultipleSelectable extends weeFormSelectable
{
	protected $aSelection;

	public function isSelected($sValue)
	{
		return !empty($this->aSelection[(string)$sValue]);
	}

	public function select($sValue, $bState = true)
	{
		$this->aSelection[$sValue] = $bState;
	}

	public function transformValue(&$aData)
	{
		if (isset($aData[(string)$this->oXML->name]))
			$a = array_flip($aData[(string)$this->oXML->name]);

		$aData[(string)$this->oXML->name] = array();

		foreach ($this->aOptions as $aOption)
			$aData[(string)$this->oXML->name][(string)$aOption['value']] = (int)isset($a[(string)$aOption['value']]);

		return true;
	}
}

?>
