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

/**
	Automatically generate and validate forms using a simple XML file.
	The generation is done using XSLT with a dynamically generated stylesheet.
*/

class weeForm implements Printable
{
	/**
		Data used to fill the form when generating it.
	*/

	protected $aData	= array();

	/**
		Error messages shown after each widget if provided.
	*/

	protected $aErrors	= array();

	/**
		Path to user stylesheets.
	*/

	protected $sUserStylesheetsPath;

	/**
		The SimpleXML object for this form.
	*/

	protected $oXML;

	/**
		Initializes the form.

		@param	$sFilename	The filename of the form XML (without path and extension).
		@param	$sAction	The action to be performed by the form (usually 'add', 'update' or 'delete').
	*/

	public function __construct($sFilename, $sAction = 'add')
	{
		class_exists('XSLTProcessor') or burn('ConfigurationException',
			'The XSL PHP extension is required by weeForm.');

		fire(!ctype_print($sAction), 'InvalidArgumentException', 'The action name must be printable.');

		$sFilename = FORM_PATH . $sFilename . FORM_EXT;
		fire(!file_exists($sFilename), 'FileNotFoundException',
			'The file ' . $sFilename . " doesn't exist.");

		$this->oXML = simplexml_load_file($sFilename);
		fire($this->oXML === false || !isset($this->oXML->widgets), 'BadXMLException',
			'The file ' . $sFilename . ' is not a valid form document.');

		if (!isset($this->oXML->uri))
			$this->oXML->addChild('uri', (!empty($_SERVER['REQUEST_URI']) ? xmlspecialchars($_SERVER['REQUEST_URI']) : null));
		if (!isset($this->oXML->formkey))
			$this->oXML->addChild('formkey', 1);

		// Delete elements with wrong action
		$this->removeNodes('//*[@action!="' . xmlspecialchars($sAction) . '"]');
	}

	/**
		Load and parse the XSL stylesheet and return it.

		This method loads every available stylesheets and include them in the resulting file.
		System stylesheets are first "imported" followed by user stylesheets getting "included".

		The form key is given to the stylesheet if any.

		@return The built XSL stylesheet.
	*/

	protected function buildXSLStylesheet()
	{
		$oWeeStylesheets = new DirectoryIterator(ROOT_PATH . 'wee/form/xslt/');
		$oUserStylesheets = empty($this->sUserStylesheetsPath) ? array() : new DirectoryIterator($this->sUserStylesheetsPath);

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

	/**
		Provide values to each widgets of the form.
		When a value has no corresponding widget, it is discarded.

		@param $aData The data used to fill the form's widgets values.
	*/

	public function fill($aData)
	{
		is_array($aData) || ($aData instanceof Mappable) or burn('InvalidArgumentException',
			'$aData must be an associative array of names and values or a Mappable object.');

		if (is_object($aData))
			$aData = $aData->toArray();

		$this->aData = $aData + $this->aData;
	}

	/**
		Provide error messages to each widgets of the form.
		When a message has no corresponding widget, it is discarded.

		@param $aData The error messages to be given to the corresponding widgets.
	*/

	public function fillErrors($aErrors)
	{
		fire(!is_array($aErrors) && !($aErrors instanceof ArrayAccess), 'InvalidArgumentException',
			'$aErrors must be an associative array of names and values.');

		$this->aErrors = $aErrors + $this->aErrors;
	}

	/**
		Removes any data sent not found in the widgets of the form.
		Helps prevent data injection vulnerabilities.

		Data injection vulnerabilities allow the attacker to inject data in a model
		without prior validation. This can be used to update a field without the developer's consent.

		For example let's say you have a form to update your profile's information. In the users
		table you have a simple field 'isadmin' to determine who's administrator. An attacker could
		submit the form with an additional 'isadmin' value set to 1, the whole POST data would be
		sent to the model and the model would save the 'isadmin' value, granting administrator
		rights to the attacker. Of course this imply that the model you are using allow the saving
		of an 'isadmin' value, which may not always be the case if you write a custom save method.

		@param $aData Data to filter.
		@return array Filtered data.
	*/

	public function filter($aData)
	{
		$aWidgets	= $this->oXML->xpath('//widget/name');
		$aNames		= array();

		foreach ($aWidgets as $oWidget)
			$aNames[(string)$oWidget] = null;

		return array_intersect_key($aData, $aNames);
	}

	/**
		Return the form's submit method.

		@return string The form's submit method. Usually 'get' or 'post'.
	*/

	public function getMethod()
	{
		return (string)$this->oXML->method;
	}

	/**
		Create and initialize an helper for the specified widget.

		@param $sHelper Class name of the helper you want to create.
		@param $sWidget Widget name in the XML, which is then selected using XPath.
		@return object The helper of the type requested.
	*/

	public function helper($sHelper, $sWidget)
	{
		fire(!ctype_print($sWidget), 'InvalidArgumentException', 'The widget name must be printable.');

		$oXML = $this->xpathOne('//widget[name="' . xmlspecialchars($sWidget) . '"]');
		return new $sHelper($oXML);
	}

	/**
		Remove every node of the form XML returned by the given XPath query.

		@param $sXPath the XPath query.
	*/

	public function removeNodes($sXPath)
	{
		foreach ($this->oXML->xpath($sXPath) as $oNode)
		{
			$oNode = dom_import_simplexml($oNode);
			$oNode->parentNode->removeChild($oNode);
		}
	}

	/**
		Set the user stylesheets path used to override default stylesheets.

		@param $sUserStylesheetsPath Path to the stylesheets to include.
	*/

	public function setUserStylesheetsPath($sUserStylesheetsPath)
	{
		$this->sUserStylesheetsPath = $sUserStylesheetsPath;
	}

	/**
		Output the form to string.

		@return string The resulting XHTML form.
	*/

	public function toString()
	{
		$oDoc = new DOMDocument();
		$oXSL = new XSLTProcessor();

		$oDoc->loadXML($this->buildXSLStylesheet());
		$oXSL->importStyleSheet($oDoc); // time consuming

		// Fill in the values and errors if any

		$aKeys = array_keys(array_merge($this->aData, $this->aErrors));
		foreach ($aKeys as $sName)
		{
			fire(!ctype_print($sName), 'InvalidArgumentException', 'The widget name must be printable.');

			$a = $this->oXML->xpath('//widget[name="' . xmlspecialchars($sName) . '"]');
			if (!empty($a))
			{
				$oWidget = $a[0];

				if (!empty($this->aData[$sName]))
				{
					if (empty($oWidget->options))
						$oWidget->value = $this->aData[$sName];
					else
					{
						$oOptionHelper = $this->helper('weeFormOptionsHelper', $sName);
						if ($oOptionHelper->isInOptions($this->aData[$sName]))
							$oOptionHelper->select($this->aData[$sName]);
					}
				}

				if (!empty($this->aErrors[$sName]))
				{
					if (!empty($oWidget->errors))
					{
						$oNode = dom_import_simplexml($oWidget->errors);
						$oNode->parentNode->removeChild($oNode);
					}
					$oWidget->addChild('errors');

					if (is_array($this->aErrors[$sName]))
						foreach ($this->aErrors[$sName] as $sMsg)
							$oWidget->errors->addChild('error', $sMsg);
					else
						$oWidget->errors->addChild('error', $this->aErrors[$sName]);
				}
			}
		}

		return $oXSL->transformToXML(dom_import_simplexml($this->oXML)->ownerDocument);
	}

	/**
		Validates the data against the form validators.

		This method first checks if the form key is valid.
		If it's not, it stops the validation and indicates there is an error.

		If an error is found an exception FormValidationException is triggered.
		Use this object to retrieve all the error messages and output them.
		You can also give the array of errors directly to the weeForm::fillErrors
		method to output all the messages after each widget.

		@param $aData The data to check (usually either $_GET or $_POST).
		@throw FormValidationException
	*/

	public function validate($aData)
	{
		$oException = new FormValidationException('The validation of the form failed. See FormValidationException::getErrors to retrieve error messages.');

		if (!defined('DEBUG') && (bool)$this->oXML->formkey)
		{
			fire(session_id() == '' || !defined('MAGIC_STRING'), 'IllegalStateException',
				'You cannot use the formkey protection without an active session.' .
				' Please either start a session (recommended) or deactivate formkey protection in the form file.');

			if (empty($aData['wee_formkey']) || empty($_SESSION['session_formkeys'][$aData['wee_formkey']]))
				$oException->addError('', _WT('Form key not found.'));
			else
			{
				// Recalculate form key to check validity

				if ($aData['wee_formkey'] != md5($_SERVER['HTTP_HOST'] . $_SESSION['session_formkeys'][$aData['wee_formkey']] . MAGIC_STRING))
					$oException->addError('', _WT('Invalid form key.'));
				else
				{
					// If form key was generated more than 6 hours ago, it is considered invalid

					$aTime = explode(' ', $_SESSION['session_formkeys'][$aData['wee_formkey']]);
					if (time() > $aTime[1] + 3600 * 6)
						$oException->addError('', _WT('Form key out of date.'));
				}
			}

			// Form has been submitted, unset the form key

			unset($_SESSION['session_formkeys'][$aData['wee_formkey']]);
		}

		// Select widgets which use validators or are required and validates data

		$aWidgets = $this->oXML->xpath('//widget[@required or validator]');

		if ($aWidgets !== false)
			foreach ($aWidgets as $oNode)
			{
				// If we don't have any data we check the required flag
				// If it's not required we skip, otherwise we note an error

				if (!array_key_exists((string)$oNode->name, $aData) ||
					(is_string($aData[(string)$oNode->name]) && !strlen($aData[(string)$oNode->name])) ||
					(is_array($aData[(string)$oNode->name]) && empty($aData[(string)$oNode->name])))
				{
					if (!empty($oNode['required']))
					{
						if (!empty($oNode['required_error']))
							$oException->addError((string)$oNode->name, _T($oNode['required_error']));
						else
							$oException->addError((string)$oNode->name, sprintf(_WT('Input is required for %s'), (string)$oNode->label));
					}

					continue;
				}

				// Then we validate the data with each validators

				foreach ($oNode->validator as $oValidatorNode)
				{
					$sClass = (string)$oValidatorNode['type'];
					@is_subclass_of($sClass, 'weeValidator')
						or burn('BadXMLException',
							sprintf(_WT('Validator %s does not exist.'), $oValidatorNode['type']));

 					$aAttributes	= (array)$oValidatorNode->attributes();
					$oValidator		= new $sClass($aAttributes['@attributes']);
					$oValidator->setValue($aData[(string)$oNode->name]);

					if ($oValidator instanceof weeFormValidator)
						$oValidator->setFormData($oNode, $aData);

					if ($oValidator->hasError())
						$oException->addError((string)$oNode->name, $oValidator->getError());
				}
			}

		if ($oException->hasErrors())
			throw $oException;
	}

	/**
		Performs an XPath query on the form XML.

		@param $sPath The XPath query to run.
		@return array The XPath result.
	*/

	public function xpath($sPath)
	{
		return $this->oXML->xpath($sPath);
	}

	/**
		Performs an XPath query on the form XML and retrieve exactly one result.
		The result has to exist otherwise an UnexpectedValueException is thrown.

		@param $sPath The XPath query to run.
		@return SimpleXMLElement The element retrieved by the query.
	*/

	public function xpathOne($sPath)
	{
		$a = $this->xpath($sPath);
		fire(sizeof($a) != 1, 'UnexpectedValueException',
			'weeForm::xpathOne expects one and only one result; it retrieved ' . sizeof($a) . '.');
		return $a[0];
	}
}
