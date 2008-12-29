<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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
	TODO

	should do weeCRUDUI but with a special list and other actions
	the list should contain a weeTreeUI (tree view) ?
	no need for a special UI class for the list itself, just a template
*/

class weeTreeEditorUI extends weeUI
{
	protected $sBaseTemplatePrefix = 'ui/crud/';

	protected $aColumns = array();

	protected $oSet;

	protected function defaultEvent($aEvent)
	{
		empty($this->aColumns) and burn('IllegalStateException',
			_WT('You must define the columns used by the tree in order to edit it.'));

		empty($this->oSet) and burn('IllegalStateException',
			_WT('You must provide a set using the setSet method before sending an event.'));

		$this->sBaseTemplate = 'tree';

		$aMeta = $this->oSet->getMeta();

		$this->set(array(
			'columns' => $this->aColumns,
			'frame' => $aEvent['frame'],
			'primary' => $aMeta['primary'],
			'tree' => $this->oSet->orderBy($this->aColumns['left'])->fetchAll(),
		));
	}

	protected function doFormEvent($aEvent, $sCallbackMethod)
	{
		$oFormUI = new weeDbMetaFormUI($this->oController);
		$oFormUI->setDbSet($this->oSet);
		$oFormUI->setSubmitCallback(array($this, $sCallbackMethod));
		$oFormUI->dispatchEvent($aEvent);

		$this->set('form', $oFormUI);
	}

	protected function eventAdd($aEvent)
	{
		empty($this->oSet) and burn('IllegalStateException', _WT('You must provide a set using the setSet method before sending an event.'));

		$this->sBaseTemplate = 'add';
		$this->doFormEvent($aEvent, 'insertRecordCallback');
	}

	protected function eventDelete($aEvent)
	{
		empty($this->oSet) and burn('IllegalStateException', _WT('You must provide a set using the setSet method before sending an event.'));
		empty($aEvent['post']) and burn('InvalidArgumentException', _WT('The event delete must be called using a POST request.'));

		$this->sBaseTemplate = 'delete';
		$this->oSet->delete($aEvent['post']);
	}

	protected function eventUpdate($aEvent)
	{
		empty($this->oSet) and burn('IllegalStateException', _WT('You must provide a set using the setSet method before sending an event.'));

		$this->sBaseTemplate = 'update';
		$this->doFormEvent($aEvent, 'updateRecordCallback');
	}

	public function insertRecordCallback($aData)
	{
		$this->oSet->insert($aData);
	}

	public function setColumns($sLabelColumn, $sLeftId, $sRightId)
	{
		$this->aColumns = array(
			'label'	=> $sLabelColumn,
			'left'	=> $sLeftId,
			'right'	=> $sRightId,
		);
	}

	public function setDbSet($mSet, $iCountPerPage = 25)
	{
		if (is_string($mSet))
			$mSet = new $mSet;

		$mSet instanceof weeDbSet or burn('InvalidArgumentException', _WT('$mSet must be an insteance of weeDbSet.'));

		$this->oSet = $mSet;
		$this->iCountPerPage = $iCountPerPage;
	}

	public function updateRecordCallback($aData)
	{
		$sModel = $this->oSet->getModelName();
		$oRecord = new $sModel($aData);
		$oRecord->update();
	}
}
