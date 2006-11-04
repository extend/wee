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

class weeFormFileInput extends weeFormWritable
{
	public function __toString()
	{
		$sHelp		= null;
		if (isset($this->oXML->help))
			$sHelp	= ' title="' . weeOutput::encodeValue(_($this->oXML->help)) . '"';

		$sMIME		= null;
		if (isset($this->oXML->mime))
			$sMIME	= ' accept="' . weeOutput::encodeValue($this->oXML->mime) . '"';

		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));
		$sName		= weeOutput::encodeValue($this->oXML->name);

		return '<label for="' . $sId . '"' . $sHelp . '>' . $sLabel . '</label> <input type="file" id="' .
			   $sId . '" name="' . $sName . '"' . $sHelp . $sMIME . '/>';
	}

	//According to this, it is not a good idea to extend from writable...
	final public function getValue()
	{
		Burn('BadMethodCallException');
	}

	final public function setValue($sValue)
	{
		Burn('BadMethodCallException');
	}

	protected function isValidXML($oXML)
	{
		return parent::isValidXML($oXML) && !isset($oXML->validator, $oXML->value);
	}
}

?>
