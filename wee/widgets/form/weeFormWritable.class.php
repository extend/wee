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

abstract class weeFormWritable extends weeFormWidget
{
	protected $sValue;

	public function __construct($oXML)
	{
		parent::__construct($oXML);

		if (isset($oXML->value))
			$this->setValue($oXML->value);
	}

	public function getValue()
	{
		return $this->sValue;
	}

	public function setValue($sNewValue)
	{
		//TODO:maybe check against validators
		$this->sValue = $sNewValue;
	}

	protected function isValidXML($oXML)
	{
		return parent::isValidXML($oXML) && isset($oXML->name, $oXML->label);
	}
}

?>
