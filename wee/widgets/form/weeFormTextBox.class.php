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
	Text input form widget (input type="text").
*/

class weeFormTextBox extends weeFormWritable
{
	/**
		Type of the input.
		Can be either text or password.
	*/

	protected $sTextType = 'text';

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

		$a			= $this->xpath('./validator[@type="weeStringValidator"]');
		$sMaxLen	= (empty($a)) ? null : ' maxlength="' . weeOutput::encodeValue($a[0]['max']) . '"';

		$sValue		= null;
		if (strlen((string)$this->sValue) > 0)
			$sValue	= ' value="' . weeOutput::encodeValue($this->getValue()) . '"';

		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));
		$sName		= weeOutput::encodeValue($this->oXML->name);

		$sInput		= '<input type="' . $this->sTextType . '" id="' . $sId . '" name="' . $sName . '"' . $sClass . $sHelp . $sMaxLen . $sValue . '/>';
		if (!empty($this->oXML->format))
			$sInput	= sprintf($this->oXML->format, $sInput);

		return '<label for="' . $sId . '"' . $sHelp . '>' . $sLabel . '</label> ' . $sInput;
	}
}

?>
