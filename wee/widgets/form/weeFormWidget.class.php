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
	Base class for form widgets.
*/

abstract class weeFormWidget
{
	/**
		Suffix to the XHTML element, in case there is more than one with the same name.
		Remember that XHTML element's id must be unique.
		This property is thus used to make all ids unique even if you output more than one time a widget.
	*/

	protected $iIdSuffix;

	/**
		The SimpleXML object describing this widget.
	*/

	protected $oXML;

	/**
		Initialize the widget using the SimpleXML object.

		@param $oXML The SimpleXML object describing the widget.
	*/

	public function __construct($oXML)
	{
		fire(!$this->isValidXML($oXML), 'BadXMLException');
		$this->oXML = $oXML;
	}

	/**
		Return the widget XHTML code.

		@return string XHTML for this widget.
	*/

	abstract public function __toString();

	/**
		Construct and return the XHTML element's id for this widget.

		@return string The XHTML element's id.
	*/

	protected function getId()
	{
		$sSuffix		= null;
		if (!empty($this->iIdSuffix))
			$sSuffix	= '_' . $this->iIdSuffix;
		$this->iIdSuffix++;

		return 'form_' . weeOutput::encodeValue($this->oXML->name) . $sSuffix;
	}

	/**
		Check if the SimpleXML object is valid for this widget.
		Only used in the constructor.

		@param	$oXML	The SimpleXML object.
		@return	bool	Whether the SimpleXML object is valid.
	*/

	protected function isValidXML($oXML)
	{
		return true;
	}

	/**
		Transform the value posted if needed.
		Return false if the value was not set.

		@param	$aData	[IN,OUT] The data sent using the form. Usually $_POST or $_GET.
		@return	bool	Whether the value is present.
	*/

	public function transformValue(&$aData)
	{
		return isset($aData[(string)$this->oXML->name]);
	}

	/**
		Perform an XPATH query on the SimpleXML object.

		@param	$sPath	The XPATH path to retrieve.
		@return	array	An array of SimpleXML objects retrieved by this query.
	*/

	public function xpath($sPath)
	{
		return $this->oXML->xpath($sPath);
	}
}

?>
