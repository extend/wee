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
	Configurable list of items.
*/

class weeListUI extends weeUI
{
	/**
		Name of the template for the frame.
	*/

	protected $sBaseTemplate = 'list';

	/**
		Columns to display in the list.

		Use the format 'label' => 'name', with 'label' optional.
	*/

	protected $aColumns = array();

	/**
		List of global actions associated with the list.
	*/

	protected $aGlobalActions = array();

	/**
		List of actions associated with each items.
	*/

	protected $aItemsActions = array();

	/**
		List of items to display for this page.
	*/

	protected $aList;

	/**
		Columns identifying an item, used for the items actions links.
	*/

	protected $aPrimaryKey;

	/**
		Total number of items in the list.
	*/

	protected $iTotal;

	/**
		Add a new global action.

		A global action is identified by the following parameters:
			- link: URL to the action's event
			- label: Label describing the action

		@param $aAction The global action parameters.
	*/

	public function addGlobalAction(array $aAction)
	{
		$this->aGlobalActions[] = $aAction;
	}

	/**
		Add a new item action.

		An item action is identified by the following parameters:
			- link: URL to the action's event
			- label: Label describing the action

		@param $aAction The item action parameters.
	*/

	public function addItemAction(array $aAction)
	{
		$this->aItemsActions[] = $aAction;
	}

	/**
		Send the list configuration and data to the template.
		Also create a weePaginationUI object for paginating the list.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		$oPagination = new weePaginationUI($this->oController);
		$oPagination->setTotal($this->iTotal);
		$oPagination->dispatchEvent($aEvent);

		$aColumns = $this->aColumns;

		if (empty($aColumns)) {
			is_array($this->aList) or burn('IllegalStateException',
				_WT('You must set the columns explicitely if the list of items contains objects.'));

			reset($this->aList);
			$aItem = current($this->aList);

//TODO:toArray

			is_array($aItem) or burn('IllegalStateException',
				_WT('You must set the columns explicitely if the list of items contains objects.'));

			$aColumns = array_keys($aItem);
		}

		$this->set(array(
			'columns' => $aColumns,
			'primary' => $this->aPrimaryKey,
			'list' => $this->aList,
			'global_actions' => $this->aGlobalActions,
			'items_actions' => $this->aItemsActions,
			'pagination' => $oPagination,
		));
	}

	/**
		Define the columns to display in the list.
		Columns use the format 'label' => 'name', with 'label' optional.

		@params $aColumns Columns to be displayed in the list.
	*/

	public function setColumns($aColumns)
	{
		$this->aColumns = $aColumns;
	}

	/**
		Define the key identifying each item uniquely.
		A key can be either one or more columns.

		@param $aPrimaryKey Key identifying each item uniquely.
	*/

	public function setPrimaryKey($aPrimaryKey)
	{
		$this->aPrimaryKey = $aPrimaryKey;
	}

	/**
		Set the data associated with the list, along with the number of items that
		are displayed per pages and the total number of items available.

		@param $aList The data to be listed.
		@param $iCountPerPage Number of items displayed per page. Optional.
		@param $iTotal Total number of items available. Optional.
	*/

	public function setList($aList, $iCountPerPage = null, $iTotal = null)
	{
		$this->aList = $aList;

		if (empty($iTotal))
			$this->iTotal = 0;
		else
			$this->iTotal = (int)ceil($iTotal / $iCountPerPage) - 1;
	}
}
