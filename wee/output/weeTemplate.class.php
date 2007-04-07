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

if (!defined('TPL_PATH'))	define('TPL_PATH',	ROOT_PATH . 'tpl/');
if (!defined('TPL_EXT'))	define('TPL_EXT',	'.tpl');

/**
	Base class for template handling.
	Load, configure and display templates.
*/

class weeTemplate
{
	/**
		Filename of the template, including path and extension.
	*/

	protected $sFilename;

	/**
		Data to be used in the template.
	*/

	protected $aData;

	/**
		Data in its encoded form.
	*/

	protected $aEncodedData;

	/**
		Configure the filename and the data for this template.

		@param $sTemplate	The template name.
		@param $aData		Data to be used in the template.
	*/

	public function __construct($sTemplate, array $aData = array())
	{
		$this->sFilename	= TPL_PATH . $sTemplate . TPL_EXT;
		fire(!file_exists($this->sFilename), 'FileNotFoundException');

		$this->aData		= $aData;
	}

	/**
		Returns the template as a string.

		TODO:this will encode data for each sub-templates, optimize

		@return string The template.
	*/

	public function __toString()
	{
		$this->aEncodedData = $this->aData;
		extract(weeOutput::encodeArray($this->aEncodedData));

		ob_start();
		require($this->sFilename);
		$s = ob_get_contents();
		ob_end_clean();

		return $s;
	}

	/**
		Adds a value to the data array.

		@param	$mName	Name of the variable inside the template.
		@param	$mValue	Value of the variable.
		@return	$this
	*/

	public function set($mName, $mValue)
	{
		$this->aData[$mName] = $mValue;
		return $this;
	}

	/**
		Creates a new template.
		Use this to create a template inside another.

		Beware: child classes should not override this function.

		@param $sTemplate	The template name.
		@param $aData		Data to be used in the template.
		@return	weeTemplate	The weeTemplate object newly created.
	*/

	protected function template($sTemplate, array $aData = array())
	{
		return new weeTemplate($sTemplate, $aData + $this->aData);
	}
}

?>
