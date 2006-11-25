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

class weeFormDateInput extends weeFormWritable
{
	public function __construct($oXML)
	{
		parent::__construct($oXML);

		if ($this->oXML->from == 'current')
			$this->oXML->from = @date('Y');
		if ($this->oXML->to == 'current')
			$this->oXML->to = @date('Y');

		fire($this->oXML->from > $this->oXML->to, 'BadXMLException');
	}

	public function __toString()
	{
		$sHelp		= null;
		if (isset($this->oXML->help))
			$sHelp	= ' title="' . weeOutput::encodeValue(_($this->oXML->help)) . '"';

		$aDate		= $this->getValueArray();
		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));

		return '<label for="' . $sId . '"' . $sHelp . '>' . $sLabel . '</label> <fieldset class="dateinput" id="form_' . $sId . '"' . $sHelp .
			'>' . $this->renderYear($sHelp, $aDate[0]) . ' ' . $this->renderMonth($sHelp, $aDate[1]) . ' ' . $this->renderDay($sHelp, $aDate[2]) . '</fieldset>';
	}

	public function getValueArray()
	{
		return explode('-', $this->getValue()) + array(0, 0, 0);
	}

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

	public function transformValue(&$aData)
	{
		if (empty($_POST[$this->oXML->name . '_year']) || empty($_POST[$this->oXML->name . '_month']) || empty($_POST[$this->oXML->name . '_day']))
			return false;

		//TODO:validates data here too
		$_POST[(string)$this->oXML->name] = sprintf('%04u-%02u-%02u', $_POST[$this->oXML->name . '_year'], $_POST[$this->oXML->name . '_month'], $_POST[$this->oXML->name . '_day']);
		unset($_POST[$this->oXML->name . '_year'], $_POST[$this->oXML->name . '_month'], $_POST[$this->oXML->name . '_day']);

		return true;
	}
}

?>
