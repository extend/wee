<?php

/*
	Web:Extend
	Copyright (c) 2006-2009 Dev:Extend

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
		file_exists($this->sFilename) or burn('FileNotFoundException',
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
		Flush the output buffer.

		This effectively tries to push all the output so far to the browser.
		All output will be sent, even buffered output.

		Sometimes the buffer can't be sent directly to the browser, because
		of the presence of certain modules or because of an old web server version.
		@see http://php.net/flush For more information about possible flush problems.
	*/

	protected function flush()
	{
		ob_flush();
		flush();
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
		$aArgs = $aArgs + $this->aLinkArgs;

		if (empty($aArgs))
			return weeOutput::encodeValue($sLink);

		$aURL = explode('?', $sLink, 2);

		if (sizeof($aURL) > 1) {
			$aOldArgs = array();
			parse_str($aURL[1], $aOldArgs);
			$aArgs = $aArgs + $aOldArgs;
		}

		$sLink = $aURL[0] . '?';

		foreach ($aArgs as $sName => $sValue)
		{
			if ($sValue instanceof Printable)
				$sValue = $sValue->toString();

			$sLink .= $sName . '=' . urlencode(weeOutput::instance()->decode($sValue)) . '&';
		}

		return weeOutput::encodeValue(substr($sLink, 0, -1));
	}

	/**
		Output the template.
	*/

	public function render()
	{
		extract(weeOutput::encodeArray($this->aData));
		require($this->sFilename);
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
		Output another template.
		Use this to embed a template inside another.

		@param $sTemplate	The template name.
		@param $aData		Data to be used in the template.
	*/

	protected function template($sTemplate, array $aData = array())
	{
		$o = new weeTemplate($sTemplate, $aData + $this->aData);
		$o->addLinkArgs($this->aLinkArgs);
		$o->render();
	}

	/**
		Returns the template as a string.

		@return string The template.
	*/

	public function toString()
	{
		ob_start();
		$this->render();
		return ob_get_clean();
	}
}
