<?php

/*
	Web:Extend
	Copyright (c) 2006, 2007, 2008 Dev:Extend

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

if (!defined('FORM_PATH'))	define('FORM_PATH',	ROOT_PATH . 'app/form/');
if (!defined('FORM_EXT'))	define('FORM_EXT',	'.form');

class weeForm implements Printable
{
	/**
		Constant for 'add' action.
	*/

	const ACTION_ADD	= 1;

	/**
		Constant for 'upd' action.
	*/

	const ACTION_UPD	= 2;

	/**
		Constant for 'del' action.
	*/

	const ACTION_DEL	= 4;


	protected $iAction;
	protected $oXML;

	public function __construct($sFilename, $iAction = weeForm::ACTION_ADD)
	{
		fire(is_null(weeOutput::instance()), 'IllegalStateException',
			'You must select an output before creating a weeForm object.');

		$sFilename = FORM_PATH . $sFilename . FORM_EXT;
		fire(!file_exists($sFilename), 'FileNotFoundException',
			'The file ' . $sFilename . " doesn't exist.");

		$this->oXML = simplexml_load_file($sFilename);//, 'weeSimpleXMLHack');
		fire($this->oXML === false || !isset($this->oXML->widgets), 'BadXMLException',
			'The file ' . $sFilename . ' is not a valid form document.');

		$this->iAction = $iAction;
		if (!isset($this->oXML->uri))
			$this->oXML->addChild('uri', $_SERVER['REQUEST_URI']);
		if (!isset($this->oXML->formkey))
			$this->oXML->addChild('formkey', 1);
	}

	protected function buildXSLStylesheet()
	{
		$oWeeStylesheets = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOT_PATH . 'wee/form/xslt/'));
		//TODO: $oUserStylesheets

		if ((int)$this->oXML->formkey)
		{
			// Create the form key and store it in the session
			// Requires both a session open and MAGIC_STRING defined
			// The form key helps prevent cross-site request forgery

			fire(session_id() == '' || !defined('MAGIC_STRING'), 'IllegalStateException',
				'You cannot use the formkey protection without an active session. ' .
				'Please either start a session (recommended) or deactivate formkey protection in the form file.');

			$sTime		= microtime();
			$sFormKey	= md5($_SERVER['HTTP_HOST'] . $sTime . MAGIC_STRING);
			$_SESSION['session_formkeys'][$sFormKey] = $sTime;

			unset($sTime); // Clean-up
		}

		ob_start();
		require(ROOT_PATH . 'wee/form/stylesheet.xsl');
		return ob_get_clean();
	}

	public function toString()
	{
		$oDoc = new DOMDocument();
		$oXSL = new XSLTProcessor();

function getLocalizedDate($sFormat, $sValue)
{
	static $aMap = array('Y' => 0, 'M' => 1, 'D' => 2);

	$aItems = explode('-', $sValue);
	return $aItems[$aMap[$sFormat[0]]]
		. $sFormat[3] . $aItems[$aMap[$sFormat[1]]]
		. $sFormat[3] . $aItems[$aMap[$sFormat[2]]];
}

$oXSL->registerPHPFunctions('getLocalizedDate');

		$oDoc->loadXML($this->buildXSLStylesheet());
		$oXSL->importStyleSheet($oDoc);

		return $oXSL->transformToXML(dom_import_simplexml($this->oXML)->ownerDocument);
	}
}
