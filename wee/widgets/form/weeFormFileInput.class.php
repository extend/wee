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
	File input widget.
*/

class weeFormFileInput extends weeFormWritable
{
	/**
		Return the current value.

		@return string The current value.
	*/

	final public function getValue()
	{
		burn('BadMethodCallException');
	}

	/**
		Check if the SimpleXML object is valid for this widget.
		Only used in the constructor.

		@param	$oXML	The SimpleXML object.
		@return	bool	Whether the SimpleXML object is valid.
	*/

	protected function isValidXML($oXML)
	{
		return parent::isValidXML($oXML) && !isset($oXML->validator, $oXML->value);
	}

	/**
		Set a new value.

		TODO:According to this, it is not a good idea to extend from writable...

		@param $sNewValue The new value.
	*/

	final public function setValue($sValue)
	{
		burn('BadMethodCallException');
	}

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function toString()
	{
		$sClass		= null;
		if (!empty($this->oXML->class))
			$sClass	= ' class="' . weeOutput::encodeValue($this->oXML->class) . '"';

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
			   $sId . '" name="' . $sName . '"' . $sClass . $sHelp . $sMIME . '/>';
	}
}

?>
