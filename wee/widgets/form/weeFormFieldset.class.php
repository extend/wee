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

class weeFormFieldset extends weeFormContainer
{
	public function __toString()
	{
		$sClass = null;
		if (!empty($this->oXML['class']))
			$sClass = ' class="' . weeOutput::encodeValue(_($this->oXML['class'])) . '"';

		$sLabel = null;
		if (!empty($this->oXML->label))
			$sLabel = '<legend>' . weeOutput::encodeValue(_($this->oXML->label)) . '</legend>';

		return '<fieldset' . $sClass . '>' . $sLabel . parent::__toString() . '</fieldset>';
	}
}

?>
