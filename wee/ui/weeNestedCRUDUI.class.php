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
	UI frame for standard CRUD operations on nested sets.

	CRUD stands for Create, Retrieve, Update, Delete.
	This frame defines events for all of these operations applicable on a given set,
	along with the operation to move a node inside the tree.
*/

class weeNestedCRUDUI extends weeCRUDUI
{
	/**
		Displays a list of all items in the set and gives links to the
		Create, Update, Move Up, Move Down, Move and Delete events.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		// Initialize the treeview table

		$oList = new weeTreeviewUI($this->oController);
		$this->addFrame('index', $oList);

		$oList->setParams($this->aParams
			+ $this->aParams['set']->getMeta()
		);

		// Set the standard action links

		$oList->addGlobalAction(array(
			'label'		=> _WT('Create'),
			'link'		=> APP_PATH . $aEvent['frame'] . '/add',
			'method'	=> 'get',
		));

		$oList->addItemAction(array(
			'label'		=> _WT('Move'),
			'link'		=> APP_PATH . $aEvent['frame'] . '/move',
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

		// Set the tree data

		$oList->setTree($this->aParams['set']->orderBy($this->aParams['columns']['leftid'])->fetchAll());

		// Call containers default event

		weeContainerUI::defaultEvent($aEvent);
	}

	/**
		Move an item in the set.

		@param $aEvent Event information.
	*/

	protected function eventMove($aEvent)
	{
		$oFormUI = new weeFormUI($this->oController);
		$this->addFrame('form', $oFormUI);

		$oFormUI->setParams($this->aParams + array('filename' => 'ui/nestedmove'));
		$oFormUI->setCallbacks(array(
			'setup' => array($this, 'setupMoveCallback'),
			'submit' => array($this, 'moveRecordCallback'),
		));

		weeContainerUI::defaultEvent($aEvent);
	}

	/**
		Default callback for the 'move' event.
		Move an item in the set.

		@param $aData Array containing the item 'id' and the new 'parent' id.
	*/

	public function moveRecordCallback($aData)
	{
		$this->aParams['set']->move($aData['id'], $aData['parent']);
	}

	/**
		Setup callback for the 'move' event.
		Fills the form with all the available parent nodes.

		// TODO: multi-column pkeys

		@param $aEvent Event information.
		@param $oForm The form for this event.
		@param $sAction The action for this form.
	*/

	public function setupMoveCallback($aEvent, $oForm, $sAction)
	{
		$aMeta = $this->aParams['set']->getMeta();
		$oMovedNode = $this->aParams['set']->fetch($aEvent['get'][$aMeta['primary'][0]]);

		$oHelper = $oForm->helper('weeFormOptionsHelper', 'parent');

		$oResults = $this->aParams['set']->fetchAll();
		foreach ($oResults as $oNode)
			if ($oNode[$aMeta['primary'][0]] != $oMovedNode[$aMeta['primary'][0]])
				$oHelper->addOption(array(
					'label' => $oNode[$this->aParams['columns']['label']],
					'value' => $oNode[$aMeta['primary'][0]],
				));

		$oForm->fill(array('id' => $oMovedNode[$aMeta['primary'][0]]));
	}
}
