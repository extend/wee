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

/**
	Generate a form from a database set using the dbmeta API.

	@todo ::toXML for easy replacement with a custom form?
*/

class weeDbMetaForm extends weeForm
{
	/**
		Initializes the form.

		Options include:
		- action:		The action to be performed by the form. Either 'add' or 'update'. Defaults to 'add'.
		- formkey:		Whether the form key mechanism should be used for added security. Defaults to true.
		- uri:			The form URI. Defaults to $_SERVER['REQUEST_URI'].
		- method:		Method of submission of the form. Usually 'get' or 'post'. Defaults to 'post'.
		- show-pkey:	Whether to show primary key fields. By default, only a hidden field is output for the 'update' action.

		@param $oSet The set to build the form for.
		@param $aOptions Options to control the building of the form.
	*/

	public function __construct($oSet, $aOptions = array('action' => 'add', 'formkey' => true))
	{
		class_exists('XSLTProcessor') or burn('ConfigurationException',
			'The XSL PHP extension is required by weeForm.');

		if (empty($aOptions['action']))
			$aOptions['action'] = 'add';

		in_array($aOptions['action'], array('add', 'update'))
			or burn('InvalidArgumentException', 'Invalid action name. Valid action names are "add" or "update".');

		$this->loadFromSet($oSet, $aOptions);

		if (empty($aOptions['uri']))
			$this->oXML->addChild('uri', (!empty($_SERVER['REQUEST_URI']) ? xmlspecialchars($_SERVER['REQUEST_URI']) : null));
		else
			$this->oXML->addChild('uri', xmlspecialchars($aOptions['uri']));

		if (!empty($aOptions['formkey']))
			$this->oXML->addChild('formkey', 1);
	}

	/**
		Add a widget to the form.

		@param $sType Widget's type.
		@param $sName Widget's name and label.
	*/

	protected function addWidget($sType, $sName)
	{
		$oChild = $this->oXML->widgets->widget->addChild('widget');
		$oChild->addAttribute('type', $sType);
		$oChild->addChild('name', $sName);
		$oChild->addChild('label', $sName);
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
		@param $aOptions Options to control the building of the form.
	*/

	protected function loadFromSet($oSet, $aOptions)
	{
		$aMeta = $oSet->getMeta();
		$aRefSets = $this->loadRefSets($oSet);

		$this->oXML = simplexml_load_string('<form><method>'
			. xmlspecialchars(array_value($aOptions, 'method', 'post'))
			. '</method><widgets><widget type="fieldset"/></widgets></form>');

		foreach ($aMeta['columns'] as $sColumn) {
			if (in_array($sColumn, $aMeta['primary'])) {
				if (!empty($aOptions['show-pkey']))
					$this->addWidget('textbox', $sColumn);
				elseif ($aOptions['action'] == 'update')
					$this->addWidget('hidden', $sColumn);
			} elseif (empty($aRefSets[$sColumn]))
				$this->addWidget('textbox', $sColumn);
			else {
				$this->addWidget('choice', $sColumn);

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
			}
		}

		$oFieldset = $this->oXML->widgets->widget->addChild('widget');
		$oFieldset->addAttribute('type', 'fieldset');
		$oFieldset->addChild('class', 'buttonsfieldset');

		if ($aOptions['action'] == 'update') {
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

			if (empty($aRef['key']) && count($aRefMeta['primary']) == 1)
				$aMap[$aRefMeta['primary'][0]] = array('set' => $oRefSet, 'meta' => $aRefMeta, 'key' => $aRefMeta['primary'][0]);
			elseif (count($aRef['key']) == 1) {
				$aKeys = array_keys($aRef['key']);
				$aValues = array_values($aRef['key']);

				$aMap[$aKeys[0]] = array('set' => $oRefSet, 'meta' => $aRefMeta, 'key' => $aValues[0]);
			}
		}

		return $aMap;
	}
}
