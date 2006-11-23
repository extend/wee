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

if (!defined('FRM_PATH'))	define('FRM_PATH',	ROOT_PATH . 'form/');
if (!defined('FRM_EXT'))	define('FRM_EXT',	'.form');

abstract class weeFormSimpleXMLIteratorBase extends SimpleXMLIterator
{
	public function widget($oWidget = null)
	{
		static $aWidgets = null;

		if (empty($this['uniqid']))
			$this['uniqid'] = uniqid();

		$sObjectName = (string)$this['uniqid'];

		fire(isset($aWidgets[$sObjectName]) && !is_null($oWidget), 'IllegalStateException');
		if (!is_null($oWidget))
			$aWidgets[$sObjectName] = $oWidget;

		fire(empty($aWidgets[$sObjectName]), 'IllegalStateException');
		return $aWidgets[$sObjectName];
	}
}

if (version_compare(phpversion(), '5.1.3', '<'))
{
	class weeFormSimpleXMLIterator extends weeFormSimpleXMLIteratorBase
	{
		public function getName()
		{
			return dom_import_simplexml($this)->nodeName;
		}
	}
}
else
{
	class weeFormSimpleXMLIterator extends weeFormSimpleXMLIteratorBase
	{
	}
}

class weeForm
{
	const ACTION_ADD	= 1;
	const ACTION_UPD	= 2;
	const ACTION_DEL	= 4;

	protected $iAction;
	protected $sClass;
	protected $sEncType;
	protected $oForm;
	protected $bFormKey;
	protected $sMethod;
	protected $sURI;
	protected $sValidationErrors;

	public function __construct($sFilename, $iAction = weeForm::ACTION_ADD)
	{
		$sFilename = FRM_PATH . $sFilename . FRM_EXT;
		fire(!file_exists($sFilename), 'FileNotFoundException');

		$oXML = simplexml_load_file($sFilename, 'weeFormSimpleXMLIterator');
		fire($oXML === false || !isset($oXML->widgets), 'BadXMLException');

		if (isset($oXML->class))	$this->sClass	= (string)$oXML->class;
		else						$this->sClass	= 'block';

		if (isset($oXML->enctype))	$this->sEncType	= (string)$oXML->enctype;

		if (isset($oXML->method))	$this->sMethod	= (string)$oXML->method;
		else						$this->sMethod	= 'post';

		if (isset($oXML->formkey))	$this->bFormKey	= (string)$oXML->formkey;
		else						$this->bFormKey	= true;

		$this->iAction		= $iAction;
		$this->oForm		= new weeFormContainer($oXML->widgets, $iAction);
		$this->SetURI($_SERVER['REQUEST_URI']);
	}

	public function __toString()
	{
		$s	= '<form action="' . weeOutput::encodeValue($this->sURI) . '" method="' . weeOutput::encodeValue($this->sMethod) .
			  '" class="' . weeOutput::encodeValue($this->sClass) . '"';
		if (!empty($this->sEncType))
			$s .= ' enctype="' . weeOutput::encodeValue($this->sEncType) . '"';
		$s .= '>';

		if ($this->bFormKey)
		{
			// Needs session to be started to use the form key
			fire(session_id() == '' || !defined('MAGIC_STRING'), 'IllegalStateException');

			$sTime	= microtime();
			$sKey	= md5($_SERVER['HTTP_HOST'] . $sTime . MAGIC_STRING);
			$_SESSION['session_formkeys'][$sKey] = $sTime;

			$s .= '<input type="hidden" name="wee_formkey" value="' . $sKey . '"/>';
		}

		return $s . $this->oForm->__toString() . '</form>';
	}

	public function fill($aData)
	{
		foreach ($aData as $sName => $mValue)
		{
			$a = $this->oForm->xpath('//name[text()="' . $sName . '"]/..');
			if (empty($a) || (!empty($a[0]['action']) && constant($a[0]['action']) != $this->iAction))
				continue;

			$oWidget = $a[0]->widget();

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

	public function getErrors()
	{
		fire(empty($this->sValidationErrors), 'InvalidArgumentException');
		return $this->sValidationErrors;
	}

	public function hasErrors(&$aData)
	{
		fire(empty($aData), 'InvalidArgumentException');

		$this->sValidationErrors = null;

		if ($this->bFormKey)
		{
			// Needs session to be started to use the form key
			fire(session_id() == '' || !defined('MAGIC_STRING'), 'IllegalStateException');

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

		//TODO:do not xpath widgets that have wrong action
		$aWidgets = $this->oForm->xpath('//widget');

		foreach ($aWidgets as $oNode)
		{
			if ((!empty($oNode['action']) && constant($oNode['action']) != $this->iAction) ||
				$oNode->widget() instanceof weeFormStatic || $oNode->widget() instanceof weeFormFileInput)
				continue;

			if (!$oNode->widget()->transformValue($aData))
			{
				$this->sValidationErrors = _('Input is incomplete') . "\r\n";
				break;
			}

			if (!empty($oNode['required']) && empty($aData[(string)$oNode->name]))
			{
				if (!empty($oNode['required_error']))	$this->sValidationErrors .= _($oNode['required_error']) . "\r\n";
				else									$this->sValidationErrors .= _('Input is required') . "\r\n";
				continue;
			}

			foreach ($oNode->validator as $oValidatorNode)
			{
				fire(!class_exists($oValidatorNode['type']), 'BadXMLException');
				$aValidatorNode		= (array)$oValidatorNode;
				$sClass				= (string)$oValidatorNode['type'];
				$oValidator			= new $sClass($aData[(string)$oNode->name], $aValidatorNode['@attributes']);
				if ($oValidator instanceof weeFormValidator)
				{
					$oValidator->setData($aData);
					$oValidator->setWidget($oNode->widget());
				}
				if ($oValidator->hasError())
					$this->sValidationErrors .= $oValidator->getError() . "\r\n";
			}
		}

		return !empty($this->sValidationErrors);
	}

	public function setClass($sClass)
	{
		$this->sClass = $sClass;
		return $this;
	}

	public function setMethod($sMethod)
	{
		Fire($sMethod != 'post' && $sMethod != 'get', 'InvalidArgumentException');
		$this->sMethod = $sMethod;
	}

	public function setURI($sURI)
	{
		//TODO:check validity
		$this->sURI = $sURI;
		return $this;
	}

	protected function sqlArrayHandler($oNode, $oQuery, $aData)
	{
		if (empty($oNode['sql-array-handler']))	$sFunc = 'sqlArrayToMultiple';
		else									$sFunc = (string)$oNode['sql-array-handler'];

		fire(!method_exists($this, $sFunc), 'BadXMLException');
		$this->$sFunc($oQuery, $aData);
	}

	protected function sqlArrayToMultiple($oQuery, $aData)
	{
		foreach ($aData as $sName => $sValue)
			$oQuery->set('`' . $sName . '`', $sValue);
	}

	protected function applyMD5($sValue)
	{
		return md5($sValue);
	}

	public function toSQL($aData, $sTable)
	{
		fire($this->iAction != weeForm::ACTION_ADD && $this->iAction != weeForm::ACTION_UPD, 'IllegalStateException');

		//TODO:do not xpath widgets that have wrong action
		//TODO:this will remove the first line of the following condition
		$aWidgets = $this->oForm->xpath('//widget');

		foreach ($aWidgets as $iKey => $oNode)
			if ((!empty($oNode['action']) && constant($oNode['action']) != $this->iAction) ||
				$oNode->widget() instanceof weeFormStatic || $oNode->widget() instanceof weeFormFileInput ||
				!empty($oNode['sql-ignore']))
				unset($aWidgets[$iKey]);

		// This will fail if there is no connected databases

		if ($this->iAction == weeForm::ACTION_ADD)	$sAction = 'insert';
		else										$sAction = 'update';

		$oQuery = §()->$sAction($sTable);

		foreach ($aWidgets as $oNode)
		{
			$sName	= (string)$oNode->name;
			$mValue	= $aData[$sName];

			if (is_array($mValue))
			{
				if (!empty($oNode['sql-apply']))
				{
					$sFunc	= (string)$oNode['sql-apply'];
					foreach ($mValue as $sItemName => $sItemValue)
						$mValue[$sItemName] = $sItemValue;
				}

				$this->sqlArrayHandler($oNode, $oQuery, $mValue);
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

	public function widget($sName)
	{
		fire(!ctype_print($sName), 'InvalidArgumentException');

		$a = $this->oForm->xpath('//name[text()="' . $sName . '"]/..');
		fire(empty($a), 'BadXMLException');
		return $a[0]->widget();
	}
}

?>
