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

class weeFormTextBox extends weeFormWritable
{
	protected $sTextType = 'text';

	public function __toString()
	{
		$sHelp		= null;
		if (isset($this->oXML->help))
			$sHelp	= ' title="' . weeOutput::encodeValue(_($this->oXML->help)) . '"';

		$sMaxLen	= null;//TODO
//Maybe something like that?
//		$aMaxLen	= $this->XPath('/validator[@type="String"]/max');

		//TODO:maxlen
		//~ $aMaxLen = $this->oNode->xpath('check[@type="maxlen"]');
		//~ if (!empty($aMaxLen))
			//~ $sOutControl	.= ' maxlength="' . $aMaxLen[0] . '"';

		$sValue		= null;
		if (!empty($this->sValue))
			$sValue	= ' value="' . weeOutput::encodeValue($this->getValue()) . '"';

		$sId		= $this->getId();
		$sLabel		= weeOutput::encodeValue(_($this->oXML->label));
		$sName		= weeOutput::encodeValue($this->oXML->name);

		return '<label for="' . $sId . '"' . $sHelp . '>' . $sLabel . '</label> <input type="' . $this->sTextType .
			   '" id="' . $sId . '" name="' . $sName . '"' . $sHelp . $sMaxLen . $sValue . '/>';
	}
}

?>
