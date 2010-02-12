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

/**
	Generate a form from a database set using the dbmeta API.
*/

class weeDbMetaForm extends weeForm
{
	/**
		Options for this dbmeta form object.

		@see weeDbMetaForm::__construct
	*/

	protected $aOptions = array();

	/**
		Initializes the form.

		Options include:
		* action:				The action to be performed by the form. Either 'add' or 'update'. Defaults to 'add'.
		* formkey:				Whether the form key mechanism should be used for added security. Defaults to true.
		* ignorecolumns:		List of columns to ignore when building the form.
		* label-from-comment:	Use the columns comment as the field's label. Defaults to true.
		* method:				Method of submission of the form. Usually 'get' or 'post'. Defaults to 'post'.
		* show-pkey:			Whether to show primary key fields. By default, only a hidden field is output for the 'update' action.
		* uri:					The form URI. Defaults to $_SERVER['REQUEST_URI'].

		@param $oSet The set to build the form for.
		@param $aOptions Options to control the building of the form.
	*/

	public function __construct($oSet, $aOptions = array())
	{
		class_exists('XSLTProcessor') or burn('ConfigurationException',
			_WT('The XSL PHP extension is required by weeForm.'));

		is_callable('simplexml_load_string') or burn('ConfigurationException',
			_WT('The SimpleXML extension is required by weeForm.'));

		$this->aOptions = $aOptions;

		if (!isset($this->aOptions['action']))
			$this->aOptions['action'] = 'add';
		if (!isset($this->aOptions['formkey']))
			$this->aOptions['formkey'] = true;
		if (!isset($this->aOptions['label-from-comment']))
			$this->aOptions['label-from-comment'] = true;
		if (!isset($this->aOptions['ignorecolumns']))
			$this->aOptions['ignorecolumns'] = array();

		in_array($this->aOptions['action'], array('add', 'update'))  or burn('InvalidArgumentException',
			_WT('Invalid action name. Valid action names are "add" or "update".'));

		$this->loadFromSet($oSet);

		if (empty($this->aOptions['uri']))
			$this->oXML->addChild('uri', (!empty($_SERVER['REQUEST_URI']) ? xmlspecialchars($_SERVER['REQUEST_URI']) : null));
		else
			$this->oXML->addChild('uri', xmlspecialchars($this->aOptions['uri']));

		if (!empty($this->aOptions['formkey']))
			$this->oXML->addChild('formkey', 1);
	}

	/**
		Add a widget to the form.

		@param $sType Widget's type.
		@param $oCol Column's metadata information.
	*/

	protected function addWidget($sType, $oCol)
	{
		$sName = $oCol->name();
		$sLabel = (!empty($this->aOptions['label-from-comment']) && $oCol instanceof weeDbMetaCommentable) ? $oCol->comment() : null;
		if (empty($sLabel))
			$sLabel = $sName;

		$oChild = $this->oXML->widgets->widget->addChild('widget');
		$oChild->addAttribute('type', $sType);
		$oChild->addChild('name', $sName);
		$oChild->addChild('label', $sLabel);

		if (!$oCol->isNullable() && !$oCol->hasDefault())
			$oChild->addAttribute('required', 'required');

		if ($sType == 'choice') {
			$oValidator = $oChild->addChild('validator');
			$oValidator->addAttribute('type', 'weeOptionValidator');
		}
	}

	/**
		Provide values to each widgets of the form.

		When a value has no corresponding widget, it is discarded.
		When a value is an empty string '', it is replaced by null.
		However, when an array contains an empty string, it is left as-is.

		@param $aData The data used to fill the form's widgets values.
	*/

	public function filter($aData)
	{
		$aData = parent::filter($aData);

		foreach ($aData as $sName => $mValue) {
			if (!is_array($mValue) && strlen($mValue) === 0)
				$aData[$sName] = null;
		}

		return $aData;
	}

	/**
		Create the form from a set metadata. Called by the class' constructor.

		@param $oSet The set to build the form for.
	*/

	protected function loadFromSet($oSet)
	{
		$aMeta = $oSet->getMeta();
		$aRefSets = $this->loadRefSets($oSet);

		$this->oXML = simplexml_load_string('<form><method>'
			. xmlspecialchars(array_value($this->aOptions, 'method', 'post'))
			. '</method><widgets><widget type="fieldset"/></widgets></form>');

		foreach ($aMeta['colsobj'] as $oCol) {
			$sColumn = $oCol->name();

			// Ignore given columns
			if (in_array($sColumn, $this->aOptions['ignorecolumns']))
				continue;

			if (!empty($aRefSets[$sColumn])) {
				$this->addWidget('choice', $oCol);

				$sLabelColumn = $aRefSets[$sColumn]['key'];
				foreach ($aRefSets[$sColumn]['meta']['columns'] as $sRefColumn)
					if (strpos($sRefColumn, 'label') !== false) {
						$sLabelColumn = $sRefColumn;
						break;
					}

				$oHelper = $this->helper('weeFormOptionsHelper', $sColumn);
				$oHelper->addOption(array('label' => 'NULL', 'value' => ''));
				foreach ($aRefSets[$sColumn]['set']->fetchAll() as $aRow)
					$oHelper->addOption(array('label' => $aRow[$sLabelColumn], 'value' => $aRow[$aRefSets[$sColumn]['key']]));
			} else {
				$sWidget = 'textbox';

				if (in_array($sColumn, $aMeta['primary']) && empty($this->aOptions['show-pkey'])) {
					if ($this->aOptions['action'] != 'update')
						continue;
					$sWidget = 'hidden';
				}
				elseif (strpos($sColumn, '_is_'))
					$sWidget = 'checkbox';
				elseif (strpos($sColumn, 'password'))
					$sWidget = 'password';

				$this->addWidget($sWidget, $oCol);
			}
		}

		$oFieldset = $this->oXML->widgets->widget->addChild('widget');
		$oFieldset->addAttribute('type', 'fieldset');
		$oFieldset->addChild('class', 'buttonsfieldset');

		if ($this->aOptions['action'] == 'update') {
			$oButton = $oFieldset->addChild('widget');
			$oButton->addAttribute('type', 'resetbutton');
		}

		$oButton = $oFieldset->addChild('widget');
		$oButton->addAttribute('type', 'submitbutton');
	}

	/**
		Load reference sets and return the ones we can use to build selectable widgets.

		@param $oSet The base set from which to load the reference sets.
		@return array An associative array mapping column names to their respective reference sets.
	*/

	protected function loadRefSets($oSet)
	{
		$aSets = $oSet->getRefSets();
		$oDb = $oSet->getDb();

		$aMap = array();

		foreach ($aSets as $aRef) {
			if (!is_array($aRef))
				$aRef = array('set' => $aRef);

			empty($aRef['set']) and burn('InvalidArgumentException', _WT('No set was given.'));

			$oRefSet = new $aRef['set'];
			$oRefSet->setDb($oDb);
			$aRefMeta = $oRefSet->getMeta();

			empty($aRefMeta['primary']) and burn('InvalidArgumentException',
				sprintf(_WT('The reference table %s do not have a primary key.'), $aRefMeta['table']));

			if (empty($aRef['key'])) {
				if (count($aRefMeta['primary']) == 1)
					$aMap[$aRefMeta['primary'][0]] = array('set' => $oRefSet, 'meta' => $aRefMeta, 'key' => $aRefMeta['primary'][0]);
			} elseif (count($aRef['key']) == 1) {
				$aKeys = array_keys($aRef['key']);
				$aValues = array_values($aRef['key']);

				$aMap[$aKeys[0]] = array('set' => $oRefSet, 'meta' => $aRefMeta, 'key' => $aValues[0]);
			}
		}

		return $aMap;
	}

	/**
		Output the form to XML.

		@return string The form generated from the set in its .form XML file format.
	*/

	public function toXML()
	{
		$oDoc = dom_import_simplexml($this->oXML)->ownerDocument;
		$oDoc->formatOutput = true;
		return $oDoc->saveXML();
	}
}
