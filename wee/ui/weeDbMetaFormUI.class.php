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
	UI frame encapsulating a weeDbMetaForm object.
*/

class weeDbMetaFormUI extends weeFormUI
{
	/**
		Set associated with the dbmeta form.
	*/

	protected $oSet;

	/**
		Defines the set to use with the dbmeta form.

		@param $mSet An object or a class name for the set.
	*/

	public function setDbSet($mSet)
	{
		if (!is_object($mSet))
			$mSet = new $mSet;

		$this->oSet = $mSet;
	}

	/**
		Setup the frame by creating the form and configuring it.

		@param $aEvent Event information.
	*/

	protected function setup($aEvent)
	{
		$this->sAction = (empty($aEvent['name'])) ? 'add' : $aEvent['name'];
		$this->oForm = new weeDbMetaForm($this->oSet, array(
			'action' => $aEvent['name'],
			'formkey' => true,
			'method' => 'post',
		));

		if ($this->sAction == 'update')
			$this->oForm->fill($this->oSet->fetch($aEvent['get']));

		if (!empty($this->mSetupCallback))
			call_user_func($this->mSetupCallback, $this->oForm, $this->sAction);
	}

	/**
		Method called when data has been submitted and validated.

		@param $aData Data submitted using the form.
	*/

	protected function submit($aData)
	{
		if (empty($this->mSubmitCallback)) {
			if ($this->sAction == 'add')
				$this->oSet->insert($aData);
			elseif ($this->sAction == 'upd') {
				$sModelName = $this->oSet->getModelName();
				$oModel = new $sModelName($aData);
				$oModel->update();
			}
		} else
			call_user_func($this->mSubmitCallback, $aData);
	}
}
