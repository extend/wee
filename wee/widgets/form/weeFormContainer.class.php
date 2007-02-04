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

class weeFormContainer extends weeFormStatic
{
	protected $iAction;

	public function __construct($oXML, $iAction)
	{
		parent::__construct($oXML);
		$this->iAction = $iAction;

		foreach ($this->oXML->widget as $oChild)
		{
			fire(empty($oChild['type']) || !class_exists($oChild['type']), 'BadXMLException');

			if (!empty($oChild['action']) && constant($oChild['action']) != $this->iAction)
				continue;

			$sClass = (string)$oChild['type'];
			$oChild->property('widget', new $sClass($oChild, $iAction));
		}
	}

	public function __toString()
	{
		$s = '<ol>';
		foreach ($this->oXML->widget as $oChild)
		{
			if (!empty($oChild['action']) && constant($oChild['action']) != $this->iAction)
				continue;

			$s .= '<li';
			if ($oChild->property('widget') instanceof weeFormHidden)
				$s .= ' class="invisible"';
			$s .= '>' . $oChild->property('widget')->__toString() . '</li>';
		}
		return $s . '</ol>';
	}

	protected function isValidXML($oXML)
	{
		return parent::isValidXML($oXML) && !empty($oXML->widget);
	}
}

?>
