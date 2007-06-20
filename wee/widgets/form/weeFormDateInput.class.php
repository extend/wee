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
	Really consist of three select elements.
*/

class weeFormDateInput extends weeFormWritable
{
	/**
		Initialize the widget using the SimpleXML object.

		@param $oXML The SimpleXML object describing the widget.
	*/

	public function __construct($oXML)
	{
		parent::__construct($oXML);

		fire(empty($this->oXML->from), 'BadXMLException');
		fire(empty($this->oXML->to), 'BadXMLException');

		if ($this->oXML->from == 'current')
			$this->oXML->from = @date('Y');
		if ($this->oXML->to == 'current')
			$this->oXML->to = @date('Y');

		fire($this->oXML->from > $this->oXML->to, 'BadXMLException');
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
		Return the XHTML for the day's select element.

		@param	$sHelp		The help message.
		@param	$iSelected	The day selected.
		@return	string		The XHTML for the day's select element.
	*/

	protected function renderDay($sHelp, $iSelected)
	{
		$s = '<select name="' . $this->oXML->name . '_day"' . $sHelp . '>';

		for ($i = 1; $i <= 31; $i++)
		{
			$s .= '<option value="' . $i . '"';
			if ($i == $iSelected)
				$s .= ' selected="selected"';
			$s .= '>' . sprintf('%02u', $i) . '</option>';
		}

		return $s . '</select>';
	}

	/**
		Return the XHTML for the month's select element.

		@param	$sHelp		The help message.
		@param	$iSelected	The month selected.
		@return	string		The XHTML for the month's select element.
	*/

	protected function renderMonth($sHelp, $iSelected)
	{
		$s = '<select name="' . $this->oXML->name . '_month"' . $sHelp . '>';

		for ($i = 1; $i <= 12; $i++)
		{
			$s .= '<option value="' . $i . '"';
			if ($i == $iSelected)
				$s .= ' selected="selected"';
			$s .= '>' . _(@date('F', @mktime(0, 0, 0, $i, 10))) . '</option>';
		}

		return $s . '</select>';
	}

	/**
		Return the XHTML for the year's select element.

		@param	$sHelp		The help message.
		@param	$iSelected	The year selected.
		@return	string		The XHTML for the year's select element.
	*/

	protected function renderYear($sHelp, $iSelected)
	{
		$s = '<select name="' . $this->oXML->name . '_year"' . $sHelp . '>';

		for ($i = (int)$this->oXML->from; $i <= (int)$this->oXML->to; $i++)
		{
			$s .= '<option value="' . $i . '"';
			if ($i == $iSelected)
				$s .= ' selected="selected"';
			$s .= '>' . $i . '</option>';
		}

		return $s . '</select>';
	}

	/**
		Set a new value.

		@overload setValue($iYear, $iMonth, $iDay) Can be called with the date values separated instead of the standard form.
		@param $sNewValue The new value.
	*/

	public function setValue($sNewValue)
	{
		if (func_num_args() == 1)
			parent::setValue($sNewValue);
		else
		{
			fire(func_num_args() != 3, 'InvalidArgumentException');

			$iYear	= func_get_arg(0);
			$iMonth	= func_get_arg(1);
			$iDay	= func_get_arg(2);

			//TODO:validates arguments

			parent::setValue(sprintf('%04u-%02u-%02u', $iYear, $iMonth, $iDay));
		}
	}

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function toString()
	{
		$sClass		= 'dateinput';
		if (!empty($this->oXML->class))
			$sClass	= weeOutput::encodeValue($this->oXML->class);

		$sHelp		= null;
		if (isset($this->oXML->help))
			$sHelp	= ' title="' . weeOutput::encodeValue(_($this->oXML->help)) . '"';

		$aDate		= $this->getValueArray();
		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));

		return '<label for="form_' . $sId . '"' . $sHelp . '>' . $sLabel . '</label> <fieldset class="' . $sClass . '" id="form_' . $sId . '"' . $sHelp .
			'>' . $this->renderYear($sHelp, $aDate[0]) . ' ' . $this->renderMonth($sHelp, $aDate[1]) . ' ' . $this->renderDay($sHelp, $aDate[2]) . '</fieldset>';
	}

	/**
		Transform the value posted if needed.
		Return false if the value was not set.

		@param	$aData	[IN,OUT] The data sent using the form. Usually $_POST or $_GET.
		@return	bool	Whether the value is present.
	*/

	public function transformValue(&$aData)
	{
		if (empty($aData[$this->oXML->name . '_year']) || empty($aData[$this->oXML->name . '_month']) || empty($aData[$this->oXML->name . '_day']))
			return false;

		//TODO:validates data here too
		$aData[(string)$this->oXML->name] = sprintf('%04u-%02u-%02u', $aData[$this->oXML->name . '_year'], $aData[$this->oXML->name . '_month'], $aData[$this->oXML->name . '_day']);
		unset($aData[$this->oXML->name . '_year'], $aData[$this->oXML->name . '_month'], $aData[$this->oXML->name . '_day']);

		return true;
	}
}

?>
