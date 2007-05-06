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
	Base class for standard input buttons.
*/

class weeFormButtonInput extends weeFormStatic
{
	/**
		Type of the input.
		Can be either button, reset or submit.
	*/

	protected $sButtonType = 'button';

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function __toString()
	{
		$sClass		= null;
		if (!empty($this->oXML->class))
			$sClass	= ' class="' . weeOutput::encodeValue($this->oXML->class) . '"';

		$sLabel	= null;
		if (isset($this->oXML->label))
			$sLabel = ' value="' . weeOutput::encodeValue(_($this->oXML->label)) . '"';

		$sName	= null;
		if (isset($this->oXML->name))
			$sName	= ' name="' . weeOutput::encodeValue($this->oXML->name) . '"';

		return '<input type="' . $this->sButtonType . '"' . $sClass . $sName . $sLabel . '/>';
	}
}

?>
