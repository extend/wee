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
	UI frame for standard CRUD operations.

	CRUD stands for Create, Retrieve, Update, Delete.
	This frame defines events for all of these operations applicable on a given set.
*/

class weeCRUDUI extends weeContainerUI
{
	/**
		Frame's parameters.
	*/

	protected $aParams = array('countperpage' => 3);

	/**
		Displays a list of all items in the set and gives links to the Create, Update and Delete events.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		$iCount = $this->aParams['set']->count();

		// Initialize the list frame

		$oList = new weeListUI($this->oController);
		$this->addFrame('index', $oList);

		$oList->setParams($this->aParams
			+ $this->aParams['set']->getMeta()
			+ array('total' => $iCount)
		);

		// Set the standard action links

		$oList->addGlobalAction(array(
			'label'		=> _WT('Create'),
			'link'		=> APP_PATH . $aEvent['frame'] . '/add',
			'method'	=> 'get',
		));

		$oList->addItemAction(array(
			'label'		=> _WT('Update'),
			'link'		=> APP_PATH . $aEvent['frame'] . '/update',
			'method'	=> 'get',
		));

		$oList->addItemAction(array(
			'label'		=> _WT('Delete'),
			'link'		=> APP_PATH . $aEvent['frame'] . '/delete',
			'method'	=> 'post',
		));

		// Set the custom list ordering

		if (!empty($aEvent['get']['orderby']) && (empty($this->aParams['columns']) || in_array($aEvent['get']['orderby'], $this->aParams['columns']))) {
			$this->aParams['set']->orderBy(array($aEvent['get']['orderby'] => array_value($aEvent['get'], 'orderdirection', 'asc')));
			$oList->setParams(array(
				'orderby' => $aEvent['get']['orderby'],
				'orderdirection' => array_value($aEvent['get'], 'orderdirection', 'asc'),
			));
		}

		// Set the list data

		$iFrom = (int)array_value($aEvent['get'], 'from', 0);
		($iFrom < 0 || $iFrom >= $iCount) and burn('OutOfRangeException',
			_WT('The parameter "from" is out of range.'));

		$oList->setList($this->aParams['set']->fetchSubset($iFrom, $this->aParams['countperpage']));

		// Call containers default event

		parent::defaultEvent($aEvent);

		// Enable AJAX responses - replace the frame by the updated template

		if ($aEvent['context'] == 'xmlhttprequest') {
			$this->noChildTaconite();
			$this->update('replace', '#' . $this->getChildIdPrefix() . 'index', $this->oTpl);
		}
	}

	/**
		Perform a form event. Common method for Create and Update.

		@param $aEvent Event information.
		@param $sCallbackMethod Function called when the form is submitted.
	*/

	protected function doFormEvent($aEvent, $sSubmitCallback)
	{
		$oFormUI = new weeDbMetaFormUI($this->oController);
		$this->addFrame('form', $oFormUI);

		$oFormUI->setParams($this->aParams);
		$oFormUI->setCallbacks(array('submit' => array($this, $sSubmitCallback)));

		parent::defaultEvent($aEvent);

		if ($aEvent['context'] == 'xmlhttprequest') {
			$this->noChildTaconite();
			$this->update('replace', '#' . $this->getChildIdPrefix() . 'index', $this->oTpl);
		}
	}

	/**
		Handles a form used to add an item to the set.

		@param $aEvent Event information.
	*/

	protected function eventAdd($aEvent)
	{
		$this->doFormEvent($aEvent, 'insertRecordCallback');
	}

	/**
		Deletes an item from the set.

		@param $aEvent Event information.
	*/

	protected function eventDelete($aEvent)
	{
		empty($aEvent['post']) and burn('InvalidArgumentException',
			_WT('The event delete must be called using a POST request.'));

		$this->sBaseTemplate = 'delete';
		$this->sBaseTemplatePrefix = 'ui/crud/';
		$this->aParams['set']->delete($aEvent['post']);

		if ($aEvent['context'] == 'xmlhttprequest')
			$this->update('eval', null, 'alert("Item deleted successfully.");'); // TODO: better, and remove the row
	}

	/**
		Handles a form used to update an item in the set.

		@param $aEvent Event information.
	*/

	protected function eventUpdate($aEvent)
	{
		$this->doFormEvent($aEvent, 'updateRecordCallback');
	}

	/**
		Default callback for the 'add' event.
		Insert an item into the set.

		@param $aData Data to be inserted.
	*/

	public function insertRecordCallback($aData)
	{
		$this->aParams['set']->insert($aData);
	}

	/**
		Define the frame's parameters.

		Parameters can include:
			- columns:		Columns to display in the list. Columns use the format 'label' => 'name', with 'label' optional.
			- countperpage:	Number of items per page in the list for the default event. Defaults to 25.
			- set:			The set where all the CRUD operations will be performed.

		@param $aParams Frame's parameters.
	*/

	public function setParams($aParams)
	{
		if (isset($aParams['set']) && is_string($aParams['set']))
			$aParams['set'] = new $aParams['set'];

		$this->aParams = $aParams + $this->aParams;
	}

	/**
		Setup the frame.
		This method is called before each event method call.

		@param $aEvent Event information
	*/

	protected function setup($aEvent)
	{
		empty($this->aParams['set']) and burn('IllegalStateException',
			_WT('You must provide a set using the method "setParams" before sending an event.'));
	}

	/**
		Default callback for the 'update' event.
		Update an item in the set.

		@param $aData Updated data.
	*/

	public function updateRecordCallback($aData)
	{
		$sModel = $this->aParams['set']->getModelName();
		$oRecord = new $sModel($aData);
		$oRecord->update();
	}
}
