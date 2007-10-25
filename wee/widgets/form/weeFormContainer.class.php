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
	Base class for container widgets.

	Container widgets can contains other widgets inside them.
	They are responsible to render them properly when the form is rendered.
*/

class weeFormContainer extends weeFormStatic
{
	/**
		The current form action.
	*/

	protected $iAction;

	/**
		Initialize the widget using the SimpleXML object.

		@param $oXML The SimpleXML object describing the widget.
	*/

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

	/**
		Check if the SimpleXML object is valid for this widget.
		Only used in the constructor.

		@param	$oXML	The SimpleXML object.
		@return	bool	Whether the SimpleXML object is valid.
	*/

	protected function isValidXML($oXML)
	{
		return parent::isValidXML($oXML) && count($oXML->widget) != 0;
	}

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function toString()
	{
		$s = '<ol>';
		foreach ($this->oXML->widget as $oChild)
		{
			if (!empty($oChild['action']) && constant($oChild['action']) != $this->iAction)
				continue;

			$s .= '<li';
			if ($oChild->property('widget') instanceof weeFormHidden)
				$s .= ' class="invisible"';
			$s .= '>' . $oChild->property('widget')->toString() . '</li>';
		}
		return $s . '</ol>';
	}
}

?>
