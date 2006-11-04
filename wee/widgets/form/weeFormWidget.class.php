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

abstract class weeFormWidget
{
	protected $iIdSuffix;
	protected $oXML;

	public function __construct($oXML)
	{
		Fire(!$this->isValidXML($oXML), 'BadXMLException');
		$this->oXML = $oXML;
	}

	public function __toString()
	{
	}

	protected function getId()
	{
		$sSuffix		= null;
		if (!empty($this->iIdSuffix))
			$sSuffix	= '_' . $this->iIdSuffix;
		$this->iIdSuffix++;

		return 'form_' . weeOutput::encodeValue($this->oXML->name) . $sSuffix;
	}

	public function transformValue(&$aData)
	{
		return isset($aData[(string)$this->oXML->name]);
	}

	protected function isValidXML($oXML)
	{
		return true;
	}

	public function xpath($sPath)
	{
		//TODO:find a way to remove @
		return @$this->oXML->xpath($sPath);
	}
}

?>
