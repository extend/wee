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
		The filename of the form XML (without path and extension).
	*/

	protected $sFilename;

	/**
		The form object.
	*/

	protected $oForm;

	/**
		Method called after setting up the frame, at the end of the `setup` method.
	*/

	protected $mSetupCallback;

	/**
		Method called when data submitted by the form is valid.
	*/

	protected $mSubmitCallback;

	/**
		Process the event and if any data was sent, validate and submit it.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		$sMethod = $this->oForm->getMethod();
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
				$this->set('errors', $e->toString());
			}
		}
	}

	//TODO:setFormFilename

	/**
		Sets the setup callback method.

		@param $mSetupCallback Callback method called at the end of `setup`.
	*/

	public function setSetupCallback($mSetupCallback = null)
	{
		$this->mSetupCallback = $mSetupCallback;
	}

	/**
		Sets the submit callback method.

		@param $mSubmitCallback Callback method called when valid data has been submitted.
	*/

	public function setSubmitCallback($mSubmitCallback = null)
	{
		$this->mSubmitCallback = $mSubmitCallback;
	}

	/**
		Initialize the form object.

		@param $aEvent Event information.
	*/

	protected function setup($aEvent)
	{
		$this->sAction = (empty($aEvent['name'])) ? 'add' : $aEvent['name'];
		$this->oForm = new weeForm($this->sFilename, $aEvent['name']);

		if (!empty($this->mSetupCallback))
			call_user_func($this->mSetupCallback, $this->oForm, $this->sAction);
	}

	/**
		Method called when data has been submitted and validated.

		@param $aData Data submitted using the form.
	*/

	protected function submit($aData)
	{
		if (!empty($this->mSubmitCallback))
			call_user_func($this->mSubmitCallback, $aData);
	}
}
