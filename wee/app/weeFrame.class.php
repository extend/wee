<?php

if (!defined('ALLOW_INCLUSION')) die;

/**
	Base class for frame objects.

	Wrap a template, dispatch and handle events.
*/

abstract class weeFrame implements Printable
{
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
		Taconite object for applying transformations to the document.
	*/

	protected $oTaconite;

	/**
		Template for the frame.
	*/

	protected $oTpl;

	/**
		Initialize the frame by loading the template.
	*/

	public function __construct()
	{
		$sTemplate = $this->sBaseTemplatePrefix;

		if (empty($this->sBaseTemplate))
			$sTemplate .= get_class($this);
		else
			$sTemplate .= $this->sBaseTemplate;

		$this->oTpl = new weeTemplate($sTemplate);
	}

	/**
		Map an event to the respective method of this class.

		@param $aEvent Event information
		@see weeApplication::dispatchEvent for event details
	*/

	public function dispatchEvent($aEvent)
	{
		$this->sContext = $aEvent['context'];

		if (empty($aEvent['event']))
			$sFunc = 'defaultEvent';
		else
			$sFunc = 'event' . $aEvent['event'];

		fire(!is_callable(array($this, $sFunc)));//TODO:404 error?
		$this->$sFunc($aEvent);
	}

	/**
		Send an event to its respective frame.
		If no context is given, current context is used.

		@param $aEvent Event information
		@see weeApplication::dispatchEvent for event details
	*/

	public function sendEvent($aEvent)
	{
		if (empty($aEvent['context']))
			$aEvent['context'] = $this->sContext;

		if (empty($aEvent['frame']) || $aEvent['frame'] == get_class($this))
			$this->dispatchEvent($aEvent);
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
		$this->oTpl->set($mName, $mValue);
	}

	/**
		Set the controller which was used to create this frame
		and dispatch the event. Controller is usually a weeApplication object.

		@param object Controller which sent the event
	*/

	public function setController($oController)
	{
		fire(!empty($this->oController));
		$this->oController = $oController;
	}

	/**
		Return the output of the template.

		@warning Taconite not tested yet.
		@return string Output of the template
	*/

	public function toString()
	{
		if ($this->sContext == 'xmlhttprequest')
		{
			header('Content-Type: text/xml');

			if (empty($this->oTaconite))
				return '<root></root>';

			$sHeader  = '<?xml version="1.0" encoding="utf-8"?>';
			$sHeader .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

			return $sHeader . $this->oTaconite->toString();
		}

		if (!empty($this->oTaconite))
			return $this->oTaconite->applyTo($this->oTpl);

		return $this->oTpl->toString();
	}

	/**
		Update the page using the specified rule.

		@warning Not tested yet.
	*/

	public function update($sMethod, $sWhere, $sWith)
	{
		if (empty($this->oTaconite))
			$this->oTaconite = weeTaconite::create();

		$this->oTaconite->addTag($sMethod, $sWhere, $sWith);
	}
}

?>
