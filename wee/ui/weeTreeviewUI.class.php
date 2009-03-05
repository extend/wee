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
	Configurable treeview.
*/

class weeTreeviewUI extends weeUI
{
	/**
		Name of the template for the frame.
	*/

	protected $sBaseTemplate = 'treeview';

	/**
		List of global actions associated with the list.
	*/

	protected $aGlobalActions = array();

	/**
		List of actions associated with each items.
	*/

	protected $aItemsActions = array();

	/**
		Tree of items to display for this page.
	*/

	protected $aTree;

	/**
		Frame's parameters.
	*/

	protected $aParams = array();

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
		// Retrieve the columns to display in the list

		$aColumns = array_value($this->aParams, 'columns');
		if (empty($aColumns)) {
			is_array($this->aTree) or burn('IllegalStateException',
				_WT('You must set the columns explicitely if the list of items contains objects.'));

			$aItem = reset($this->aTree);
			// TODO: Mappable?

			is_array($aItem) or burn('IllegalStateException',
				_WT('You must set the columns explicitely if the list of items contains objects.'));

			$aColumns = array_keys($aItem);
		}

		// Send values to the template

		$this->set(array(
			'columns'			=> $aColumns,
			'primary'			=> array_value($this->aParams, 'primary'),
			'tree'				=> $this->aTree,
			'global_actions'	=> $this->aGlobalActions,
			'items_actions'		=> $this->aItemsActions,
		));

		// Enable AJAX responses - replace the frame by the updated template

		if ($aEvent['context'] == 'xmlhttprequest') {
			$this->noChildTaconite();
			$this->update('replaceContent', '#' . $this->sId, $this->oTpl);
		}
	}

	/**
		Define the frame's parameters.

		Parameters can include:
			- columns:			Columns to use for the tree. Must have three keys: label, leftid and rightid.
			- countperpage:		Number of items per page.
			- primary:			The key identifying each item uniquely. A key can be either one or more columns stored in an array.
			- total:			Total number of items.

		@param $aParams Frame's parameters.
	*/

	public function setParams($aParams)
	{
		$this->aParams = $aParams + $this->aParams;
	}

	/**
		Set the data associated with the list.

		@param $aTree The data to be listed.
	*/

	public function setTree($aTree)
	{
		$this->aTree = $aTree;
	}
}
