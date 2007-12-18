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
	Table container widget.
*/

class weeFormTable extends weeFormContainer
{
	/**
		The current form action.
	*/

	protected $iAction;

	/**
		Authorized XHTML tags.
	*/

	protected $aAuthorizedTags = array(
		'thead',
		'tbody',
		'tfooter',
		'tr',
		'th',
		'td',
	);

	/**
		Initialize the widget using the SimpleXML object.
		XML validation is done while initializing childrens in childrensInit.

		@param $oXML The SimpleXML object describing the widget.
	*/

	public function __construct($oXML, $iAction)
	{
		$this->iAction	= $iAction;
		$this->oXML		= $oXML;

		$this->childrensInit($this->oXML);
	}

	/**
		Initialize recursively the childrens of the given element.

		@param $oXML The element which childrens are going to be created.
	*/

	protected function childrensInit($oXML)
	{
		foreach ($oXML->children() as $oChild)
		{
			if (!empty($oChild['action']) && constant($oChild['action']) != $this->iAction)
				continue;

			if (in_array($oChild->getName(), $this->aAuthorizedTags))
				$this->childrensInit($oChild);
			elseif ($oChild->getName() == 'widget')
			{
				fire(empty($oChild['type']) || !class_exists($oChild['type']), 'BadXMLException',
					'Form widget ' . $oChild['type'] . ' do not exist.');

				$sClass = (string)$oChild['type'];
				$oChild->property('widget', new $sClass($oChild, $this->iAction));
			}
		}
	}

	/**
		Return the given element's childrens XHTML code.

		@param	$oXML	The element which childrens are going to be converted to string.
		@return	string	XHTML for the childrens of this element.
	*/

	protected function childrensToString($oXML)
	{
		$sChildrens = null;

		foreach ($oXML->children() as $oChild)
		{
			if (!empty($oChild['action']) && constant($oChild['action']) != $this->iAction)
				continue;

			if (in_array($oChild->getName(), $this->aAuthorizedTags))
			{
				$a = $oChild->xpath('*');
				if (empty($a))
					$sChildrens .= $oChild->asXML();
				else
				{
					$sAttributes = null;
					foreach ($oChild->attributes() as $sName => $sValue)
						$sAttributes .= ' ' . $sName . '="' . $sValue . '"';

					$sChildrens .= '<' . $oChild->getName() . $sAttributes . '>' . $this->childrensToString($oChild) . '</' . $oChild->getName() . '>';
				}
			}
			elseif ($oChild->getName() == 'widget')
				$sChildrens .= $oChild->property('widget')->toString();
		}

		return $sChildrens;
	}

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function toString()
	{
		$sClass = null;
		if (!empty($this->oXML->class))
			$sClass = ' class="' . $this->oXML->class . '"';

		$sLabel = null;
		if (!empty($this->oXML->label))
			$sLabel = '<caption>' . $this->oXML->label . '</caption>';

		return '<table' . $sClass . '>' . $sLabel . $this->childrensToString($this->oXML) . '</table>';
	}
}

?>
