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
	Time input widget.
*/

class weeFormTimeInput extends weeFormWritable
{
	/**
		Initialize the widget using the SimpleXML object.

		@param $oXML The SimpleXML object describing the widget.
	*/

	public function __construct($oXML)
	{
		parent::__construct($oXML);
	}

	/**
		Returns the date value in the form of an array.

		@return array The date in the form of an array. Offset 0 is the year, 1 is the month, and 2 is the day.
	*/

	public function getValueArray()
	{
		return explode(':', $this->getValue()) + array(0, 0);
	}

	/**
		Return the XHTML for the month's select element.

		@param	$sHelp		The help message.
		@param	$iSelected	The month selected.
		@return	string		The XHTML for the month's select element.
	*/

	protected function renderHour($sHelp, $iSelected)
	{
		$s = '<select name="' . $this->oXML->name . '_hour"' . $sHelp . '>';

		for ($i = 0; $i <= 23; $i++)
		{
			$s .= '<option value="' . $i . '"';
			if ($i == $iSelected)
				$s .= ' selected="selected"';
			$s .= '>' . sprintf('%02u', $i) . '</option>';
		}

		return $s . '</select>';
	}

	/**
		Return the XHTML for the day's select element.

		@param	$sHelp		The help message.
		@param	$iSelected	The day selected.
		@return	string		The XHTML for the day's select element.
	*/

	protected function renderMinute($sHelp, $iSelected)
	{
		$s = '<select name="' . $this->oXML->name . '_minute"' . $sHelp . '>';

		for ($i = 0; $i <= 59; $i += 5)
		{
			$s .= '<option value="' . $i . '"';
			if ($i == $iSelected)
				$s .= ' selected="selected"';
			$s .= '>' . sprintf('%02u', $i) . '</option>';
		}

		return $s . '</select>';
	}

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function toString()
	{
		$sClass		= 'timeinput';
		if (!empty($this->oXML->class))
			$sClass	= weeOutput::encodeValue($this->oXML->class);

		$sHelp		= null;
		if (isset($this->oXML->help))
			$sHelp	= ' title="' . weeOutput::encodeValue(_($this->oXML->help)) . '"';

		$aTime		= $this->getValueArray();
		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));

		return '<label for="form_' . $sId . '"' . $sHelp . '>' . $sLabel . '</label> <fieldset class="' . $sClass . '" id="form_' . $sId . '"' . $sHelp .
			'>' . $this->renderHour($sHelp, $aTime[0]) . ':' . $this->renderMinute($sHelp, $aTime[1]) . '</fieldset>';
	}

	/**
		Transform the value posted if needed.
		Return false if the value was not set.

		@param	$aData	[IN,OUT] The data sent using the form. Usually $_POST or $_GET.
		@return	bool	Whether the value is present.
	*/

	public function transformValue(&$aData)
	{
		//TODO:validates data here too?
		$aData[(string)$this->oXML->name] = sprintf('%02u:%02u', $aData[$this->oXML->name . '_hour'], $aData[$this->oXML->name . '_minute']);
		unset($aData[$this->oXML->name . '_hour'], $aData[$this->oXML->name . '_minute']);
		return true;
	}
}

?>
