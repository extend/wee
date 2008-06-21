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

if (!defined('FORM_PATH'))	define('FORM_PATH',	ROOT_PATH . 'app/form/');
if (!defined('FORM_EXT'))	define('FORM_EXT',	'.form');

/**
	Forms handling.
	Creates and manages forms based on an XML template.
	Validates automatically data posted and adds security checks.
*/

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

	/**
		The current form action.
	*/

	protected $iAction;

	/**
		Arrays mapping actions to their string names.
	*/

	protected $iActionMap = array(
		1 => 'ACTION_ADD',
		2 => 'ACTION_UPD',
		4 => 'ACTION_DEL',
	);

	/**
		The 'class' attribute for the XHTML 'form' element.
	*/

	protected $sClass = 'block';

	/**
		The 'enctype' attribute for the XHTML 'form' element.
	*/

	protected $sEncType;

	/**
		A weeFormContainer object which serves as the root element of the form.
	*/

	protected $oForm;

	/**
		Determines if the form key must be defined for this form or not.
	*/

	protected $bFormKey = true;

	/**
		The 'method' attribute for the XHTML 'form' element.
	*/

	protected $sMethod = 'post';

	/**
		The URI where the data will be sent.
	*/

	protected $sURI;

	/**
		Contains the last validation errors.
		Used by hasErrors and getErrors only.
	*/

	protected $sValidationErrors;

	/**
		Initializes the form.

		@param	$sFilename	The filename of the form XML (without path and extension).
		@param	$iAction	The action to be performed by the form (usually add, update or delete).
	*/

	public function __construct($sFilename, $iAction = weeForm::ACTION_ADD)
	{
		fire(is_null(weeOutput::instance()), 'IllegalStateException',
			'You must select an output before creating a weeForm object.');

		$sFilename = FORM_PATH . $sFilename . FORM_EXT;
		fire(!file_exists($sFilename), 'FileNotFoundException',
			'The file ' . $sFilename . " doesn't exist.");

		$oXML = simplexml_load_file($sFilename, 'weeSimpleXMLHack');
		fire($oXML === false || !isset($oXML->widgets), 'BadXMLException',
			'The file ' . $sFilename . ' is not a valid XML document.');

		if (isset($oXML->class))	$this->sClass	= (string)$oXML->class;
		if (isset($oXML->enctype))	$this->sEncType	= (string)$oXML->enctype;
		if (isset($oXML->formkey))	$this->bFormKey	= (string)$oXML->formkey;
		if (isset($oXML->method))	$this->sMethod	= (string)$oXML->method;

		$this->iAction	= $iAction;
		$this->oForm	= new weeFormContainer($oXML->widgets, $iAction);
		$this->setURI($_SERVER['REQUEST_URI']);
	}

	/**
		Returns the md5 of the specified string.

		@param	$sValue	The specified string.
		@return	string	The md5 of the specified string.
	*/

	protected function applyMD5($sValue)
	{
		return md5($sValue);
	}

	/**
		Returns the specified string trimmed of its spaces and tabulations.

		@param	$sValue	The specified string.
		@return	string	The specified string trimmed.
	*/

	protected function applyTrim($sValue)
	{
		return trim($sValue);
	}

	/**
		Fill widgets of the form based on the data.
		When a data has no corresponding widget, it is skipped.

		@param $aData The data used to fill the form's widgets.
	*/

	public function fill($aData)
	{
		fire(!is_array($aData) && !($aData instanceof ArrayAccess), 'InvalidArgumentException',
			'$aData must be an associative array of names and values.');

		foreach ($aData as $sName => $mValue)
		{
			$a = $this->oForm->xpath('//name[text()="' . $sName . '"]/..');
			if (empty($a) || (!empty($a[0]['action']) && constant($a[0]['action']) != $this->iAction))
				continue;

			$oWidget = $a[0]->property('widget');

			if ($oWidget instanceof weeFormCheckable)
				$oWidget->check($mValue);
			elseif ($oWidget instanceof weeFormOneSelectable)
				$oWidget->select($mValue);
			elseif ($oWidget instanceof weeFormMultipleSelectable)
			{
				foreach ($mValue as $sItem => $bState)
					$oWidget->select($sItem, $bState);
			}
			elseif ($oWidget instanceof weeFormWritable && !($oWidget instanceof weeFormPasswordBox || $oWidget instanceof weeFormFileInput))
				$oWidget->setValue($mValue);
		}
	}

	/**
		Returns the current action constant name.

		@return string The current action constant name.
	*/

	protected function getActionString()
	{
		$o = new ReflectionClass(__CLASS__);
		$aConstants = array_flip($o->getConstants());
		return $aConstants[$this->iAction];
	}

	/**
		Returns errors found by hasErrors.
		You MUST call hasErrors before calling getErrors.

		@return string The errors found by the hasErrors method.
	*/

	public function getErrors()
	{
		fire(empty($this->sValidationErrors), 'InvalidArgumentException',
			'There was no error while validating the data. Please call weeForm::getErrors only if weeForm::hasErrors returns true.');
		return $this->sValidationErrors;
	}

	/**
		Validates the data against the form schema.

		If an error is found the detailed error message can be found using getErrors.
		However, this message is built using the form schema, and is designed to be printed on the user screen.

		This method also checks if the form key is valid.
		If it's not, it stops the validation and indicates there is an error.

		@param	$aData	The data to check (usually either $_GET or $_POST).
		@return	bool	True if there IS errors, false otherwise.
	*/

	public function hasErrors(&$aData)
	{
		fire(empty($aData), 'InvalidArgumentException', '$aData must not be empty.');

		$this->sValidationErrors = null;

		if (!defined('DEBUG') && $this->bFormKey)
		{
			fire(session_id() == '' || !defined('MAGIC_STRING'), 'IllegalStateException',
				'You cannot use the formkey protection without an active session.' .
				' Please either start a session (recommended) or deactivate formkey protection in the form file.');

			if (empty($aData['wee_formkey']) || empty($_SESSION['session_formkeys'][$aData['wee_formkey']]))
				$this->sValidationErrors = _('Form key not found.') . "\r\n";
			else
			{
				// Recalculate form key to check validity
				if ($aData['wee_formkey'] != md5($_SERVER['HTTP_HOST'] . $_SESSION['session_formkeys'][$aData['wee_formkey']] . MAGIC_STRING))
					$this->sValidationErrors = _('Invalid form key.') . "\r\n";
				else
				{
					// If form key was generated more than 6 hours ago, it is considered invalid
					$aTime = explode(' ', $_SESSION['session_formkeys'][$aData['wee_formkey']]);
					if (time() > $aTime[1] + 3600 * 6)
						$this->sValidationErrors = _('Form key out of date.') . "\r\n";
				}
			}

			// Form has been submitted, unset the form key
			unset($_SESSION['session_formkeys'][$aData['wee_formkey']]);

			if (!empty($this->sValidationErrors))
				return true;
		}

		foreach ($this->xpathSelectWidgets() as $oNode)
		{
			if ($oNode->property('widget') instanceof weeFormStatic ||
				$oNode->property('widget') instanceof weeFormFileInput)
				continue;

			$oNode->property('widget')->transformValue($aData);

			if (!empty($oNode['required']) && empty($aData[(string)$oNode->name]))
			{
				if (!empty($oNode['required_error']))	$this->sValidationErrors .= _($oNode['required_error']) . "\r\n";
				else									$this->sValidationErrors .= _('Input is required') . "\r\n";
				continue;
			}

			foreach ($oNode->validator as $oValidatorNode)
			{
				fire(!class_exists($oValidatorNode['type']), 'BadXMLException',
					'Validator ' . $oValidatorNode['type'] . ' do not exist.');

				$aValidatorNode		= (array)$oValidatorNode;
				$sClass				= (string)$oValidatorNode['type'];
				$oValidator			= new $sClass($aData[(string)$oNode->name], $aValidatorNode['@attributes']);
				if ($oValidator instanceof weeFormValidator)
				{
					$oValidator->setData($aData);
					$oValidator->setWidget($oNode->property('widget'));
				}
				if ($oValidator->hasError())
					$this->sValidationErrors .= $oValidator->getError() . "\r\n";
			}
		}

		return !empty($this->sValidationErrors);
	}

	/**
		Sets the class of the XHTML 'form' element.

		@param	$sClass	The element class.
		@return	$this
	*/

	public function setClass($sClass)
	{
		$this->sClass = $sClass;
		return $this;
	}

	/**
		Sets the method for posting the form data.
		It is the 'method' attribute of the XHTML 'form' element.

		@param $sMethod The method used. Must be either post or get.
	*/

	public function setMethod($sMethod)
	{
		fire($sMethod != 'post' && $sMethod != 'get', 'InvalidArgumentException',
			'Method ' . $sMethod . " is not allowed. Please use either 'post' or 'get'.");
		$this->sMethod = $sMethod;
	}

	/**
		Sets the form destination URI.
		The default URI is the script itself.

		@return $this
	*/

	public function setURI($sURI)
	{
		$this->sURI = $sURI;
		return $this;
	}

	/**
		Maps each field of the data array to one columns of the table.

		@param	$oQuery	The query object used to build the SQL query.
		@param	$aData	The data to map.
	*/

	protected function sqlArrayToMultiple($oQuery, $aData)
	{
		foreach ($aData as $sName => $sValue)
			$oQuery->set('`' . $sName . '`', $sValue);
	}

	/**
		Automatically converts data from the form to an SQL insert or update.
		Uses the form action to determine if it is an insert or an update.

		@param	$aData				The data from the form.
		@param	$sTable				The table where the data must be sent.
		@return	weeDatabaseQuery	A weeDatabaseQuery (or child class) object for building the query.
	*/

	public function toSQL($aData, $sTable)
	{
		fire($this->iAction != weeForm::ACTION_ADD && $this->iAction != weeForm::ACTION_UPD, 'IllegalStateException',
			'weeForm::toSQL must not be called if action is neither weeForm::ACTION_ADD nor weeForm::ACTION_UPD.');

		$aWidgets = $this->xpathSelectWidgets();

		foreach ($aWidgets as $iKey => $oNode)
			if ($oNode->property('widget') instanceof weeFormStatic ||
				$oNode->property('widget') instanceof weeFormFileInput ||
				!empty($oNode['sql-ignore']))
				unset($aWidgets[$iKey]);

		// This will fail if there is no connected databases

		if ($this->iAction == weeForm::ACTION_ADD)	$sAction = 'insert';
		else										$sAction = 'update';

		$oQuery = new weeDatabaseQuery::$queryClass;
		$oQuery->$sAction($sTable);

		foreach ($aWidgets as $oNode)
		{
			$sName	= (string)$oNode->name;

			if (!isset($aData[$sName]))
				continue;

			$mValue	= $aData[$sName];

			if (is_array($mValue))
			{
				if (!empty($oNode['sql-apply']))
				{
					$sFunc	= (string)$oNode['sql-apply'];
					foreach ($mValue as $sItemName => $sItemValue)
						$mValue[$sItemName] = $this->$sFunc($sItemValue);
				}

				if (empty($oNode['sql-array-handler']))	$sFunc = 'sqlArrayToMultiple';
				else									$sFunc = (string)$oNode['sql-array-handler'];

				fire(!method_exists($this, $sFunc), 'BadXMLException',
					'The specified sql-array-handler, ' . $sFunc . ', do not exist.');
				$this->$sFunc($oQuery, $mValue);
			}
			else
			{
				if (!empty($oNode['sql-apply']))
				{
					$sFunc	= (string)$oNode['sql-apply'];
					$mValue	= $this->$sFunc($mValue);
				}

				$oQuery->set('`' . $sName . '`', $mValue);
			}
		}

		return $oQuery;
	}

	/**
		Prints the form.
		Generates a form key if needed.

		@return string The XHTML form.
	*/

	public function toString()
	{
		$s	= '<form action="' . weeOutput::encodeValue($this->sURI) . '" method="' . weeOutput::encodeValue($this->sMethod) .
			  '" class="' . weeOutput::encodeValue($this->sClass) . '"';
		if (!empty($this->sEncType))
			$s .= ' enctype="' . weeOutput::encodeValue($this->sEncType) . '"';
		$s .= '>';

		if ($this->bFormKey)
		{
			fire(session_id() == '' || !defined('MAGIC_STRING'), 'IllegalStateException',
				'You cannot use the formkey protection without an active session.' .
				' Please either start a session (recommended) or deactivate formkey protection in the form file.');

			$sTime	= microtime();
			$sKey	= md5($_SERVER['HTTP_HOST'] . $sTime . MAGIC_STRING);
			$_SESSION['session_formkeys'][$sKey] = $sTime;

			$s .= '<input type="hidden" name="wee_formkey" value="' . $sKey . '"/>';
		}

		return $s . $this->oForm->toString() . '</form>';
	}

	/**
		Gets the specified widgets.

		@param	$sName			The name of the widget.
		@return	weeFormWidget	The widget requested.
	*/

	public function widget($sName)
	{
		fire(!ctype_print($sName), 'InvalidArgumentException', 'The widget name must be printable.');

		$a = $this->oForm->xpath('//name[text()="' . $sName . '"]/..');
		fire(empty($a), 'BadXMLException', 'Widget ' . $sName . ' not found.');
		return $a[0]->property('widget');
	}

	/**
		Execute an XPath query returning the widgets corresponding to the current action.

		@return array All the widgets corresponding to the current action.
	*/

	protected function xpathSelectWidgets()
	{
		$sXPath = '//widget[not(@action) or @action=' . $this->iAction;
		if (!empty($this->iActionMap[$this->iAction]))
			$sXPath .= ' or @action="weeForm::' . $this->iActionMap[$this->iAction] . '"';
		$sXPath .= ']';

		return $this->oForm->xpath($sXPath);
	}
}

?>
