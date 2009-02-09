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
		Number of items to display per page in the default event.
	*/

	protected $iCountPerPage;

	/**
		Columns to display for each item in the default event.
	*/

	protected $aListColumns = array();

	/**
		Target set of the CRUD operations.
	*/

	protected $oSet;

	/**
		Displays a list of all items in the set and gives links to the Create, Update and Delete events.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		empty($this->oSet) and burn('IllegalStateException', _WT('You must provide a set using the setSet method before sending an event.'));

		$iPage = (int)array_value($aEvent['get'], 'page', 0);
		$iCount = $this->oSet->count();
		$iTotal = (int)ceil($iCount / $this->iCountPerPage) - 1;

		if ($iPage < 0 || $iCount == 0)
			$iPage = 0;
		elseif ($iPage > $iTotal)
			$iPage = $iTotal;

		$oList = new weeListUI($this->oController);
		$this->addFrame('index', $oList);

		$aMeta = $this->oSet->getMeta();
		$oList->setPrimaryKey($aMeta['primary']);

		if (empty($this->aListColumns))
			// TODO: if no custom columns were provided, get the correct labels for reference sets
			$oList->setColumns($aMeta['columns']);
		else
			$oList->setColumns($this->aListColumns);

		$oList->addGlobalAction(array(
			'label' => _WT('Create'),
			'link' => APP_PATH . $aEvent['frame'] . '/add',
			'method' => 'get',
		));

		$oList->addItemAction(array(
			'label' => _WT('Update'),
			'link' => APP_PATH . $aEvent['frame'] . '/update',
			'method' => 'get',
		));

		$oList->addItemAction(array(
			'label' => _WT('Delete'),
			'link' => APP_PATH . $aEvent['frame'] . '/delete',
			'method' => 'post',
		));

		if (!empty($aEvent['get']['orderby']) && in_array($aEvent['get']['orderby'], $this->aListColumns)) {
			$this->oSet->orderBy(array($aEvent['get']['orderby'] => array_value($aEvent['get'], 'orderdirection', 'asc')));
			$oList->setOrder($aEvent['get']['orderby'], array_value($aEvent['get'], 'orderdirection', 'asc'));
		}

		$oList->setList($this->oSet->fetchSubset(
			$iPage * $this->iCountPerPage,
			$this->iCountPerPage
		), $this->iCountPerPage, $iCount);

		parent::defaultEvent($aEvent);

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

	protected function doFormEvent($aEvent, $sCallbackMethod)
	{
		$oFormUI = new weeDbMetaFormUI($this->oController);
		$this->addFrame('form', $oFormUI);

		$oFormUI->setDbSet($this->oSet);
		$oFormUI->setSubmitCallback(array($this, $sCallbackMethod));

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
		empty($this->oSet) and burn('IllegalStateException', _WT('You must provide a set using the setSet method before sending an event.'));
		$this->doFormEvent($aEvent, 'insertRecordCallback');
	}

	/**
		Deletes an item from the set.

		@param $aEvent Event information.
	*/

	protected function eventDelete($aEvent)
	{
		empty($this->oSet) and burn('IllegalStateException', _WT('You must provide a set using the setSet method before sending an event.'));
		empty($aEvent['post']) and burn('InvalidArgumentException', _WT('The event delete must be called using a POST request.'));

		$this->sBaseTemplate = 'delete';
		$this->sBaseTemplatePrefix = 'ui/crud/';
		$this->oSet->delete($aEvent['post']);

		if ($aEvent['context'] == 'xmlhttprequest')
			$this->update('eval', null, 'alert("Item deleted successfully.");'); // TODO: better, and remove the row
	}

	/**
		Handles a form used to update an item in the set.

		@param $aEvent Event information.
	*/

	protected function eventUpdate($aEvent)
	{
		empty($this->oSet) and burn('IllegalStateException', _WT('You must provide a set using the setSet method before sending an event.'));
		$this->doFormEvent($aEvent, 'updateRecordCallback');
	}

	/**
		Default callback for the 'add' event.
		Insert an item into the set.

		@param $aData Data to be inserted.
	*/

	public function insertRecordCallback($aData)
	{
		$this->oSet->insert($aData);
	}

	/**
		Defines the set where all the CRUD operations will be performed.

		@param $mSet Object or class name of the set.
		@param $iCountPerPage TODO:move that in its own method, it's stupid otherwise
	*/

	public function setDbSet($mSet, $iCountPerPage = 25)
	{
		if (is_string($mSet))
			$mSet = new $mSet;

		$mSet instanceof weeDbSet or burn('InvalidArgumentException', _WT('$mSet must be an insteance of weeDbSet.'));

		$this->oSet = $mSet;
		$this->iCountPerPage = $iCountPerPage;
	}

	/**
		Defines the columns to display in the list in the default event.

		@param $aColumns Array of 'label' => 'name' of the columns to be shown.
	*/

	public function setListColumns($aColumns)
	{
		$this->aListColumns = $aColumns;
	}

	/**
		Default callback for the 'update' event.
		Update an item in the set.

		@param $aData Updated data.
	*/

	public function updateRecordCallback($aData)
	{
		$sModel = $this->oSet->getModelName();
		$oRecord = new $sModel($aData);
		$oRecord->update();
	}
}
