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
	UI frame encapsulating a weeForm object.
*/

class weeFormUI extends weeUI
{
	/**
		The action to be performed by the form (usually 'add', 'update' or 'delete').
	*/

	protected $sAction;

	/**
		Name of the template for the frame.
	*/

	protected $sBaseTemplate = 'form';

	/**
		Callback methods associated with this frame.
	*/

	protected $aCallbacks = array();

	/**
		The form object.
	*/

	protected $oForm;

	/**
		Frame's parameters.
	*/

	protected $aParams = array();

	/**
		Process the event and if any data was sent, validate and submit it.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		$sMethod = (string)$this->oForm->xml()->method;
		($sMethod == 'get' || $sMethod == 'post') or burn('InvalidArgumentException',
			_WT('weeFormUI can only be used for GET or POST submit methods.'));

		$this->set(array(
			'form' => $this->oForm,
			'action' => $this->sAction,
		));

		if (!empty($aEvent[$sMethod])) {
			$this->set('is_submitted', true);

			$aData = $this->oForm->filter($aEvent[$sMethod]);

			try {
				$this->oForm->validate($aData);
				$this->submit($aData);
			} catch (FormValidationException $e) {
				$this->oForm->fill($aData);
				$this->oForm->fillErrors($e);
				$this->set('errors', $e);
			}
		}
	}

	/**
		Set callback methods.

		Possible callbacks are:
			- setup:	Called at the end of the method `setup`.
			- submit:	Called when valid data has been submitted.

		@param $aCallbacks Array containing (name => callback) associations.
	*/

	public function setCallbacks($aCallbacks = array())
	{
		$this->aCallbacks = $aCallbacks + $this->aCallbacks;
	}

	/**
		Define the frame's parameters.

		Parameters can include:
			- filename: Form's filename.

		@param $aParams Frame's parameters.
	*/

	public function setParams($aParams)
	{
		$this->aParams = $aParams + $this->aParams;
	}

	/**
		Initialize the form object.

		@param $aEvent Event information.
	*/

	protected function setup($aEvent)
	{
		empty($this->aParams['filename']) and burn('IllegalStateException',
			_WT('You must provide a form filename using the method "setParams" before sending an event.'));

		$this->sAction = (empty($aEvent['name'])) ? 'add' : $aEvent['name'];
		$this->oForm = new weeForm($this->aParams['filename'], $this->sAction);

		if (!empty($this->aCallbacks['setup']))
			call_user_func($this->aCallbacks['setup'], $aEvent, $this->oForm, $this->sAction);
	}

	/**
		Method called when data has been submitted and validated.

		@param $aData Data submitted using the form.
	*/

	protected function submit($aData)
	{
		if (!empty($this->aCallbacks['submit']))
			call_user_func($this->aCallbacks['submit'], $aData);
	}
}
