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
	Date input widget.

	A simple textbox that takes date as input.
	You can use jQuery UI's Calendar widget to make it more user-friendly.
*/

class weeFormDateInput extends weeFormTextBox
{
	/**
		Date format with the first three character being the year/month/day position
		and the last being the separator.

		If used with the jQuery calendar plugin, this value must be the same as the
		dateFormat option.

		TODO:instead of setting it inside each XML, maybe add it to a localization
		module that would handle all these kind of stuff automatically (at least for
		the default value).
	*/

	protected $sDateFormat = 'MDY/';

	/**
		Initialize the widget using the SimpleXML object.

		@param $oXML The SimpleXML object describing the widget.
	*/

	public function __construct($oXML)
	{
		parent::__construct($oXML);

		if (!empty($oXML->{'date-format'}))
			$this->sDateFormat = (string)$oXML->{'date-format'};
	}

	/**
		Returns the value as it will be displayed in the XHTML,
		after all the localization needed.

		@return string Value as displayed in the XHTML
	*/

	public function getLocalizedValue()
	{
		static $aMap = array('Y' => 0, 'M' => 1, 'D' => 2);

		$aItems = explode('-', $this->sValue);
		return $aItems[$aMap[$this->sDateFormat[0]]]
			. $this->sDateFormat[3] . $aItems[$aMap[$this->sDateFormat[1]]]
			. $this->sDateFormat[3] . $aItems[$aMap[$this->sDateFormat[2]]];
	}

	/**
		Returns the date value in the form of an array.

		@return array The date in the form of an array. Offset 0 is the year, 1 is the month, and 2 is the day.
	*/

	public function getValueArray()
	{
		return explode('-', $this->getValue()) + array(0, 0, 0);
	}

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function toString()
	{
		$sClass		= ' class="dateinput"';
		if (!empty($this->oXML->class))
			$sClass	= ' class="' . weeOutput::encodeValue($this->oXML->class) . '"';

		$sHelp		= null;
		if (isset($this->oXML->help))
			$sHelp	= ' title="' . weeOutput::encodeValue(_($this->oXML->help)) . '"';

		$sValue		= null;
		if (strlen((string)$this->sValue) > 0)
			$sValue	= ' value="' . weeOutput::encodeValue($this->getLocalizedValue()) . '"';

		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));
		$sName		= weeOutput::encodeValue($this->oXML->name);

		return '<label for="' . $sId . '"' . $sHelp . '>' . $sLabel . '</label> <input type="' . $this->sTextType .
			'" id="' . $sId . '" name="' . $sName . '" maxlength="10"' . $sClass . $sHelp . $sValue . '/>';
	}

	/**
		Transform the value posted if needed.

		@param	$aData	[IN,OUT] The data sent using the form. Usually $_POST or $_GET.
	*/

	public function transformValue(&$aData)
	{
		if (empty($aData[(string)$this->oXML->name]))
			return;

		$aItems = explode($this->sDateFormat[3], $aData[(string)$this->oXML->name]);
		$aData[(string)$this->oXML->name] = $aItems[strpos($this->sDateFormat, 'Y')]
			. '-' . $aItems[strpos($this->sDateFormat, 'M')]
			. '-' . $aItems[strpos($this->sDateFormat, 'D')];
	}
}

?>
