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

if (!defined('TPL_PATH'))	define('TPL_PATH',	ROOT_PATH . 'app/tpl/');
if (!defined('TPL_EXT'))	define('TPL_EXT',	'.tpl');

/**
	Base class for template handling.
	Load, configure and display templates.
*/

class weeTemplate implements Printable
{
	/**
		Data to be used in the template.
	*/

	protected $aData;

	/**
		Data in its encoded form.
	*/

	protected $aEncodedData;

	/**
		Filename of the template, including path and extension.
	*/

	protected $sFilename;

	/**
		Array containing predefined values to be added to the link parameters.
	*/

	protected $aLinkArgs = array();

	/**
		Configure the filename and the data for this template.

		@param $sTemplate	The template name.
		@param $aData		Data to be used in the template.
	*/

	public function __construct($sTemplate, array $aData = array())
	{
		$this->sFilename	= TPL_PATH . $sTemplate . TPL_EXT;
		fire(!file_exists($this->sFilename), 'FileNotFoundException',
			'The file ' . $this->sFilename . " doesn't exist.");

		$this->aData		= $aData;
	}

	/**
		Add new values to the parameters to be added to links created
		using the method mkLink.

		@param $aArgs Parameters to be added.
	*/

	public function addLinkArgs($aArgs)
	{
		$this->aLinkArgs = $aArgs + $this->aLinkArgs;
	}

	/**
		Create a link using a base url (which may or may not contain parameters)
		and the values predefined previously and/or given by the $aArgs arguments.

		@param	$sLink Base url.
		@param	$aArgs Parameters to be added.
		@return string Link newly created with the given parameters added at the end.
	*/

	protected function mkLink($sLink, $aArgs = array())
	{
		$sSeparator = (strpos($sLink, '?') === false) ? '?' : '&amp;';

		$aArgs = $aArgs + $this->aLinkArgs;
		foreach ($aArgs as $sName => $sValue)
		{
			$sLink .= $sSeparator . $sName . '=' . $sValue;
			$sSeparator = '&amp;';
		}

		return $sLink;
	}

	/**
		Adds a value to the data array.

		If first parameter is an array, the array values will be
		set with their corresponding keys. If values already exist,
		they will be replaced by these from this array.

		@param	$mName	Name of the variable inside the template.
		@param	$mValue	Value of the variable.
		@return	$this
	*/

	public function set($mName, $mValue = null)
	{
		if (is_array($mName))
			$this->aData = $mName + $this->aData;
		else
			$this->aData[$mName] = $mValue;

		return $this;
	}

	/**
		Creates a new template.
		Use this to create a template inside another.

		Beware: child classes should not override this function.

		@param $sTemplate	The template name.
		@param $aData		Data to be used in the template.
		@return	string		The output of the template.
	*/

	protected function template($sTemplate, array $aData = array())
	{
		$o = new weeTemplate($sTemplate, $aData + $this->aData);
		$o->addLinkArgs($this->aLinkArgs);

		return $o->toString();
	}

	/**
		Returns the template as a string.

		TODO:this will encode data for each sub-templates, optimize

		@return string The template.
	*/

	public function toString()
	{
		$this->aEncodedData = $this->aData;
		extract(weeOutput::encodeArray($this->aEncodedData));

		ob_start();
		require($this->sFilename);
		return ob_get_clean();
	}
}

?>
