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
	UI frame for authentication.

	The default login form can handle auth drivers which use
	identifier and password credentials.
*/

class weeAuthUI extends weeContainerUI
{
	/**
		The frame's parameters.
	*/

	protected $aParams = array('authform' => 'ui/login');

	/**
		Callback methods associated with this frame.
	*/

	protected $aCallbacks = array();

	/**
		Default callback for the default event.
		Authenticate the user.

		@param $aEvent $aData
	*/

	public function auth($aData)
	{
		try {
			$aData = $this->aParams['authdriver']->authenticate($aData);
		} catch (AuthenticationException $eAuth) {
			$eForm = new FormValidationException;
			$eForm->addError('', $eAuth->getMessage());
			throw $eForm;
		}

		if (isset($this->aCallbacks['login']))
			call_user_func($this->aCallbacks['login'], $aData);
	}

	/**
		The default event of the frame.

		@param $aEvent Event informations.
		@throw IllegalStateException Parameter "authdriver" is missing.
	*/

	protected function defaultEvent($aEvent)
	{
		isset($this->aParams['authdriver']) or burn('IllegalStateException',
			sprintf(_WT('Parameter "%s" is missing.'), 'authdriver'));

		$oFormUI = new weeFormUI($this->oController);
		$this->addFrame('form', $oFormUI);

		$oFormUI->setParams($this->aParams + array('filename' => $this->aParams['authform']));
		$oFormUI->setCallbacks(array('submit' => array($this, 'auth')));

		parent::defaultEvent($aEvent);
	}

	/**
		Set callback methods.

		Possible callbacks are:
		 * login: Called at the end of the method `auth`.

		@param $aCallbacks Array containing (name => callback) associations.
	*/

	public function setCallbacks($aCallbacks = array())
	{
		$this->aCallbacks = $aCallbacks + $this->aCallbacks;
	}

	/**
		Define the frame's parameters.

		Parameters can include:
		 * authdriver:	The auth driver used for the operations.
		 * authform:	The filename of the form used for the login operation.

		@param $aParams Frame's parameters.
		@throw InvalidArgumentException Parameter "authdriver" is not an instance of weeAuth.
	*/

	public function setParams($aParams)
	{
		if (isset($aParams['authdriver']))
			is_object($aParams['authdriver']) && $aParams['authdriver'] instanceof weeAuth or burn('InvalidArgumentException',
				sprintf(_WT('Parameter "%s" must be an instance of %s.'), 'authdriver', 'weeAuth'));

		$this->aParams = $aParams + $this->aParams;
	}
}
