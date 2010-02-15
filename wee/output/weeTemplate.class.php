<?php

/*
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

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

class weeTemplate extends weeDataHolder implements weeRenderer
{
	/**
		Filename of the template, including path and extension.
	*/

	protected $sFilename;

	/**
		The MIME Type of the template.
	*/

	protected $sMIMEType;

	/**
		Configure the filename and the data for this template.

		@param $sTemplate	The template name.
		@param $aData		Data to be used in the template.
	*/

	public function __construct($sTemplate, array $aData = array())
	{
		$this->sFilename = TPL_PATH . $sTemplate . TPL_EXT;

		file_exists($this->sFilename) or burn('FileNotFoundException',
			sprintf(_WT('The file "%s" does not exist.'), $this->sFilename));

		parent::__construct($aData);
		$this->setMIMEType('text/html');
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
		if (ob_get_level() > 0)
			ob_flush();
		flush();
	}

	/**
		Return the MIME type of the output of the template.

		The default MIME type is text/html.

		@return string the MIME type of the output of the template.
	*/

	public function getMIMEType()
	{
		return $this->sMIMEType;
	}

	/**
		Output the template.
	*/

	public function render()
	{
		extract($this->toArray());
		require($this->sFilename);
	}

	/**
		Set the MIME type of the template.

		If the new MIME type is known to weeTemplate, a correct encoder is
		automatically selected.

		@param $sMIMEType The MIME type of the template.
	*/

	public function setMIMEType($sMIMEType)
	{
		if ($sMIMEType == $this->sMIMEType)
			return;

		static $aEncoders = array(
			'application/x-latex'	=> 'weeLaTeXEncoder',
			'application/xml'		=> 'weeXMLEncoder',
			'text/html'		=> 'weeXHTMLEncoder',
			'text/plain'	=> 'weeTextEncoder',
		);

		$this->sMIMEType = $sMIMEType;
		$this->setEncoder(isset($aEncoders[$sMIMEType])
			? new $aEncoders[$sMIMEType]
			: null);
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
		$o->setMIMEType($this->getMIMEType());
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

	/**
		Output a correctly encoded URL.
		Use this from the inside of the template instead of creating the object directly.

		@param $sBaseURL The base of the resulting URL.
		@param $aData The data to be appended.
		@see weeURL
	*/

	protected function url($sBaseURL = null, $aData = array())
	{
		$oURL = new weeURL($sBaseURL);
		$oURL->setEncoder($this->getEncoder());
		$oURL->addData($aData);
		echo $oURL->toString();
	}
}
