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
	Configurable list of items.
*/

class weeListUI extends weeContainerUI
{
	/**
		Name of the template for the frame.
	*/

	protected $sBaseTemplate = 'list';

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
		Frame's parameters.
	*/

	protected $aParams = array('orderdirection' => 'asc');

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
		// Initialize the pagination frame

		$oPagination = new weePaginationUI($this->oController);
		$oPagination->setParams(array('total' => $this->iTotal));
		$this->addFrame('pagination', $oPagination);

		// Retrieve the columns to display in the list

		$aColumns = array_value($this->aParams, 'columns');
		if (empty($aColumns)) {
			is_array($this->aList) or burn('IllegalStateException',
				_WT('You must set the columns explicitely if the list of items contains objects.'));

			$aItem = reset($this->aList);
			// TODO: Mappable?

			is_array($aItem) or burn('IllegalStateException',
				_WT('You must set the columns explicitely if the list of items contains objects.'));

			$aColumns = array_keys($aItem);
		}

		// Send values to the template

		$this->set(array(
			'columns'			=> $aColumns,
			'primary'			=> array_value($this->aParams, 'primary'),
			'list'				=> $this->aList,
			'global_actions'	=> $this->aGlobalActions,
			'items_actions'		=> $this->aItemsActions,
			'orderby'			=> array_value($this->aParams, 'orderby'),
			'orderdirection'	=> $this->aParams['orderdirection'],
		));

		parent::defaultEvent($aEvent);
	}

	/**
		Define the frame's parameters.

		Parameters can include:
			- columns:			Columns to display in the list. Columns use the format 'label' => 'name', with 'label' optional.
			- orderby:			The column to use to sort the rows.
			- orderdirection:	The direction of the column sort. Defaults to 'asc'.
			- primary:			The key identifying each item uniquely. A key can be either one or more columns stored in an array.

		@param $aParams Frame's parameters.
	*/

	public function setParams($aParams)
	{
		$this->aParams = $aParams + $this->aParams;
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
