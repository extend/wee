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
	UI frame encapsulating a weeDbMetaForm object.
*/

class weeDbMetaFormUI extends weeFormUI
{
	/**
		Define the frame's parameters.

		Parameters can include:
			- set: Set used for the form.

		@param $aParams Frame's parameters.
	*/

	public function setParams($aParams)
	{
		if (isset($aParams['set']) && is_string($aParams['set']))
			$aParams['set'] = new $aParams['set'];

		$this->aParams = $aParams + $this->aParams;
	}

	/**
		Setup the frame by creating the form and configuring it.

		@param $aEvent Event information.
	*/

	protected function setup($aEvent)
	{
		empty($this->aParams['set']) and burn('IllegalStateException',
			_WT('You must provide a set using the method "setParams" before sending an event.'));

		$this->sAction = (empty($aEvent['name'])) ? 'add' : $aEvent['name'];
		$this->oForm = new weeDbMetaForm($this->aParams['set'], array(
			'action' => $aEvent['name'],
			'formkey' => true,
			'method' => 'post',
		));

		$this->set('debug', defined('DEBUG'));
		if (array_value($aEvent['get'], 'output') == 'xml')
			$this->set('xmloutput', $this->oForm->toXML());

		if ($this->sAction == 'update')
			$this->oForm->fill($this->aParams['set']->fetch($aEvent['get']));

		if (!empty($this->mSetupCallback))
			call_user_func($this->mSetupCallback, $aEvent, $this->oForm, $this->sAction);
	}

	/**
		Method called when data has been submitted and validated.

		@param $aData Data submitted using the form.
	*/

	protected function submit($aData)
	{
		if (empty($this->mSubmitCallback)) {
			if ($this->sAction == 'add')
				$this->aParams['set']->insert($aData);
			elseif ($this->sAction == 'update') {
				$sModelName = $this->aParams['set']->getModelName();
				$oModel = new $sModelName($aData);
				$oModel->update();
			}
		} else
			call_user_func($this->mSubmitCallback, $aData);
	}
}
