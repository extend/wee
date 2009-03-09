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
			_WT('The XSL PHP extension is required by weeForm.'));

		ctype_print($sAction) or burn('InvalidArgumentException', _WT('The action name must be printable.'));

		$sFilename = FORM_PATH . $sFilename . FORM_EXT;
		file_exists($sFilename) or burn('FileNotFoundException',
			sprintf(_WT('The file "%s" does not exist.'), $sFilename));

		// simplexml_load_file triggers a warning if the file is not a well-formed XML.
		$this->oXML = @simplexml_load_file($sFilename);
		if ($this->oXML === false)
			throw new BadXMLException(
				sprintf(_WT('File "%s" is not a well-formed XML document.'), $sFilename),
				libxml_get_last_error()
			);
		isset($this->oXML->widgets) or burn('BadXMLException',
			sprintf(_WT('The file "%s" is not a valid form document.'), $sFilename));

		if (!isset($this->oXML->formkey))
			$this->oXML->addChild('formkey', 1);
		if (!isset($this->oXML->method))
			$this->oXML->addChild('method', 'post');
		if (!isset($this->oXML->uri))
			$this->oXML->addChild('uri', (!empty($_SERVER['REQUEST_URI']) ? xmlspecialchars($_SERVER['REQUEST_URI']) : null));

		// Delete elements with wrong action
		$this->removeNodes('//*[@action!="' . xmlspecialchars($sAction) . '"]');

		// Replace the external tags with their respective nodes
		$this->loadExternals();
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

			if (session_id() == '')
				safe_session_start();

			$sFormKey = md5(uniqid(rand(), true));
			$_SESSION['session_formkeys'][$sFormKey] = microtime();
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
			_WT('$aData must be an associative array of names and values or a Mappable object.'));

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
		is_array($aErrors) || ($aErrors instanceof Mappable) or burn('InvalidArgumentException',
			_WT('$aErrors must be an associative array of names and values or a Mappable object.'));

		if (is_object($aErrors))
			$aErrors = $aErrors->toArray();

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
		$aWidgets = $this->oXML->xpath('//widget/name');
		if ($aWidgets === false)
			return array();

		$aNames = array();

		foreach ($aWidgets as $oWidget)
			$aNames[(string)$oWidget] = null;

		// Do not filter the form key if it is enabled
		if ((int)$this->oXML->formkey)
			$aNames['wee_formkey'] = null;

		return array_intersect_key($aData, $aNames);
	}

	/**
		Create and initialize an helper for the specified widget.

		@param $sHelper Class name of the helper you want to create.
		@param $sWidget Widget name in the XML, which is then selected using XPath.
		@return object The helper of the type requested.
	*/

	public function helper($sHelper, $sWidget)
	{
		ctype_print($sWidget) or burn('InvalidArgumentException', _WT('The widget name must be printable.'));

		$oXML = $this->xpathOne('//widget[name="' . xmlspecialchars($sWidget) . '"]');
		return new $sHelper($oXML);
	}

	/**
		Load external sources.

		Currently the only external source type available is the 'set' source type.
		@see weeForm::loadExternalsFromSet
		@todo Write unit tests.
	*/

	protected function loadExternals()
	{
		$aExternals = $this->xpath('//external');

		foreach ($aExternals as $oNode) {
			empty($oNode['type']) and burn('BadXMLException',
				_WT('An "external" node is missing its "type" attribute.'));

			$sMethod = 'loadExternalsFrom' . (string)$oNode['type'];
			is_callable(array($this, $sMethod)) or burn('BadXMLException',
				sprintf(_WT('The "external" node type "%s" is invalid.'), (string)$oNode['type']));

			$this->$sMethod($oNode);
		}
	}

	/**
		Load external definitions for the set source type.

		The 'set' source type is using the format class::method and will instantiate
		the class before calling the method (it's not a static call!). This source
		type allows adding option groups and items directly taken from a set.

		@param $oNode The external node.
	*/

	protected function loadExternalsFromSet($oNode)
	{
		list($sClass, $sMethod) = explode('::', (string)$oNode['source']);
		class_exists($sClass) or burn('BadXMLException',
			sprintf(_WT('The set class "%s" do not exist.'), $sClass));

		$oNode = dom_import_simplexml($oNode);

		// We use a container node for convenience
		$oExternal = $oNode->ownerDocument->createElement('root');

		$oSet = new $sClass;
		$this->loadSetExternalsFromArray($oExternal, $oSet->$sMethod());

		// Only insert the contents of the convenience node
		// We apparently must copy the object before because a DOMNodeList is alive
		// and some operations can remove nodes from the list (like insertBefore).
		// @see http://php.net/manual/en/domnodelist.item.php#76718 and below

		$aNodes = array();
		for ($i = 0; $i < $oExternal->childNodes->length; $i++)
			$aNodes[] = $oExternal->childNodes->item($i);

		foreach ($aNodes as $oNewNode)
			$oNode->parentNode->insertBefore($oNewNode, $oNode);

		// Remove the external itself
		$oNode->parentNode->removeChild($oNode);
	}

	/**
		Load the array returned by the external source set.

		@param $oExternal Load the items into this node.
		@param $aItems The items to load.
	*/

	protected function loadSetExternalsFromArray($oExternal, $aItems)
	{
		foreach ($aItems as $aItem) {
			$sName = array_value($aItem, 'name', 'item');
			unset($aItem['name']);

			$oNewNode = $oExternal->ownerDocument->createElement($sName);

			// First load the children nodes
			if ($sName == 'group') {
				$this->loadSetExternalsFromArray($oNewNode, $aItem['children']);
				unset($aItem['children']);
			}

			// Then the attributes
			foreach ($aItem as $sName => $sValue)
				$oNewNode->setAttribute($sName, $sValue);

			// And finally append it
			$oExternal->appendChild($oNewNode);
		}
	}

	/**
		Remove every node of the form XML returned by the given XPath query.

		@param $sXPath the XPath query.
	*/

	public function removeNodes($sXPath)
	{
		$aNodes = $this->oXML->xpath($sXPath);

		if ($aNodes !== false)
			foreach ($aNodes as $oNode)
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
			// For global errors (namely errors with an empty string as widget name), we create
			// an ordered list at the beginning of the form and put the errors inside it.

			if (empty($sName)) {
				$a = $this->xpath('//widgets/ol[class="errors"]');

				if (!empty($a))
					$oErrorDiv = $a[0];
				else {
					$oNode = $this->xpathOne('//widgets');
					$oNode = dom_import_simplexml($oNode);

					$oErrorOl = $oNode->ownerDocument->createElement('ol');
					$oErrorOl->setAttribute('class', 'errors');

					$oNode->insertBefore($oErrorOl, $oNode->firstChild);
					$oErrorOl = simplexml_import_dom($oErrorOl);
				}

				if (is_array($this->aErrors[$sName]))
					foreach ($this->aErrors[$sName] as $sMsg)
						$oErrorOl->addChild('li', $sMsg);
				else
					$oErrorOl->addChild('li', $this->aErrors[$sName]);

				continue;
			}

			// If it isn't a global error we fill values and errors normally

			ctype_print($sName) or burn('InvalidArgumentException', _WT('The widget name must be printable.'));

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
		$oException = new FormValidationException(_WT('The validation of the form failed. You can retrieve error messages as a string using toString or an array using toArray.'));

		if ((bool)$this->oXML->formkey)
		{
			if (session_id() == '')
				safe_session_start();

			if (empty($aData['wee_formkey']) || empty($_SESSION['session_formkeys'][$aData['wee_formkey']]))
				$oException->addError('', _WT('Invalid form key. You probably already submitted this form.'));
			else
			{
				// If form key was generated more than 6 hours ago, it is considered invalid

				$aTime = explode(' ', $_SESSION['session_formkeys'][$aData['wee_formkey']]);
				if (time() > $aTime[1] + 3600 * 6)
					$oException->addError('', _WT('Form key out of date. Please try submitting the form again.'));
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

				if (!isset($aData[(string)$oNode->name]) ||
					(is_string($aData[(string)$oNode->name]) && !strlen($aData[(string)$oNode->name])) ||
					(is_array($aData[(string)$oNode->name]) && empty($aData[(string)$oNode->name])))
				{
					if (!empty($oNode['required']))
					{
						if (!empty($oNode['required_error']))
							$oException->addError((string)$oNode->name, _T($oNode['required_error']));
						else
							$oException->addError((string)$oNode->name, sprintf(_WT('Input is required for the field %s.'), (string)$oNode->label));
					}

					continue;
				}

				// Then we validate the data with each validators

				foreach ($oNode->validator as $oValidatorNode)
				{
					$sClass = (string)$oValidatorNode['type'];
					class_exists($sClass) && is_subclass_of($sClass, 'weeValidator') or burn('BadXMLException',
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
		Return the SimpleXML object for this form.

		@return SimpleXML SimpleXML object defining the form.
	*/

	public function xml()
	{
		return $this->oXML;
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
		$a === false || sizeof($a) != 1 and burn('UnexpectedValueException',
			sprintf(_WT('weeForm::xpathOne expects one and only one result; it retrieved %d.'), $a === false ? 0 : sizeof($a)));
		return $a[0];
	}
}
