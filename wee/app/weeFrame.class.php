<?php

/**
	Web:Extend
	Copyright (c) 2007-2008 Dev:Extend

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
	Base class for frame objects.

	Wrap a template, dispatch and handle events.
*/

abstract class weeFrame implements Printable
{
	/**
		An event has been correctly dispatched.
	*/

	const EVENT_DISPATCHED = 1;

	/**
		An unauthorized access occured.
	*/

	const UNAUTHORIZED_ACCESS = 2;

	/**
		Name of the template for the frame.
		If not defined its value will be the name of the frame's class.
	*/

	protected $sBaseTemplate;

	/**
		Prefix to the template for the frame.
		Define the template file path prefix, as in:
			$sTemplate = $sBaseTemplatePrefix . $sBaseTemplate;
	*/

	protected $sBaseTemplatePrefix;

	/**
		Context of the event.
		Used to determine what we must return to the browser.
	*/

	protected $sContext;

	/**
		Controller which sent the event, usually weeApplication.
		Also the controller used when an event is sent from this frame to another.
	*/

	protected $oController;

	/**
		The current status of the frame.
	*/

	protected $iStatus = 0;

	/**
		Taconite object for applying transformations to the document.
	*/

	protected $oTaconite;

	/**
		Template for the frame.
	*/

	protected $oTpl;

	/**
		Map an event to the respective method of this class.

		@param $aEvent Event information
		@see weeApplication::dispatchEvent for event details
	*/

	public function dispatchEvent($aEvent)
	{
		fire($this->iStatus != 0, 'IllegalStateException',
			_('An attempt to dispatch an event already occured.'));

		$this->sContext		= array_value($aEvent, 'context');
		$sFunc				= empty($aEvent['name']) ? 'defaultEvent' : 'event' . $aEvent['name'];
		fire(!is_callable(array($this, $sFunc)), 'UnexpectedValueException',
			(empty($aEvent['name']) ? _('The default event does not exist.') : sprintf(_('The event "%s" does not exist.'), $aEvent['name'])));

		try
		{
			$this->setup($aEvent);
			$this->$sFunc($aEvent);

			$this->iStatus = self::EVENT_DISPATCHED;
		}
		catch (UnauthorizedAccessException $e)
		{
			$this->iStatus = self::UNAUTHORIZED_ACCESS;
			$this->unauthorizedAccess($aEvent);
		}
	}

	/**
		Return the status of the frame.

		@return integer The status of the frame.
		@see EVENT_DISPATCHED
		@see UNAUTHORIZED_ACCESS
	*/

	public function getStatus()
	{
		return $this->iStatus;
	}

	/**
		Load a template for this frame.

		@param $sTemplate		Name of the template to load, overriding the property $sBaseTemplate if not null.
		@param $sTemplatePrefix	Prefix to the template, overriding the property $sBaseTemplatePrefix if not null.
	*/

	protected function loadTemplate($sTemplate = null, $sTemplatePrefix = null)
	{
		$sTemplatePath = (empty($sTemplatePrefix)) ? $this->sBaseTemplatePrefix : $sTemplatePrefix;

		if (empty($sTemplate))
			$sTemplatePath .= (empty($this->sBaseTemplate)) ? get_class($this) : $this->sBaseTemplate;
		else
			$sTemplatePath .= $sTemplate;

		$this->oTpl = new weeTemplate($sTemplatePath);
	}

	/**
		Send an event to its respective frame.
		If no context is given, current context is used.

		If the sent event is in the same frame, the event is performed directly without sending it to the controller.

		@param $aEvent Event information
		@see weeApplication::dispatchEvent for event details
	*/

	public function sendEvent($aEvent)
	{
		if (empty($aEvent['context']))
			$aEvent['context'] = $this->sContext;

		if (empty($aEvent['frame']) || $aEvent['frame'] == get_class($this))
		{
			$this->dispatchEvent($aEvent);
			$this->iStatus == self::UNAUTHORIZED_ACCESS
				and burn('IllegalStateException',
					_('An UnauthorizedAccessException was thrown while trying to send an event from within the frame. All events sent using sendEvent must be authorized.'));
			$this->iStatus = 0;
		}
		else
			$this->oController->dispatchEvent($aEvent);
	}

	/**
		Wrapper for weeTemplate::set method.

		If first parameter is an array, the array values will be
		set with their corresponding keys. If values already exist,
		they will be replaced by these from this array.

		@param	$mName	Name of the variable inside the template
		@param	$mValue	Value of the variable
		@see weeTemplate::set for details
	*/

	public function set($mName, $mValue = null)
	{
		if (empty($this->oTpl))
			$this->loadTemplate();

		$this->oTpl->set($mName, $mValue);
	}

	/**
		Set the controller which was used to create this frame
		and dispatch the event. Controller is usually a weeApplication object.

		@param object Controller which sent the event
	*/

	public function setController($oController)
	{
		fire(!empty($this->oController), 'UnexpectedValueException', _('$oController must not be empty.'));
		$this->oController = $oController;
	}

	/**
		Setup the frame.

		This method is called before each event method call.
	*/

	protected function setup($aEvent)
	{
	}

	/**
		Return the output of the template.

		@warning Taconite not tested yet.
		@return string Output of the template
	*/

	public function toString()
	{
		fire($this->iStatus != self::EVENT_DISPATCHED, 'IllegalStateException',
			_('An event must be dispatched before calling toString().'));

		if ($this->sContext == 'xmlhttprequest' && !empty($this->oTaconite))
		{
			header('Content-Type: text/xml');

			$sHeader  = '<?xml version="1.0" encoding="utf-8"?>';
			$sHeader .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

			return $sHeader . $this->oTaconite->toString();
		}

		if (empty($this->oTpl))
			$this->loadTemplate();

		if (!empty($this->oTaconite))
			return $this->oTaconite->applyTo($this->oTpl);

		return $this->oTpl->toString();
	}

	/**
		Method called when the user have not access to the specified frame/event.
		The process will stop after this method returns.

		@param	$aEvent	Event information
	*/

	protected function unauthorizedAccess($aEvent)
	{
	}

	/**
		Update the page using the specified rule.

		@warning Not tested yet.
	*/

	public function update($sMethod, $sWhere, $sWith = null)
	{
		if (empty($this->oTaconite))
			$this->oTaconite = weeTaconite::create();

		$this->oTaconite->addTag($sMethod, $sWhere, $sWith);
	}
}
