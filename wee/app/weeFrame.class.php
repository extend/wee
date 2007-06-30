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
		You must give a value to this variable in child classes.
	*/

	protected $sBaseTemplate;

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

//	protected $oTaconite;

	/**
		Template for the frame.
	*/

	protected $oTpl;

	/**
		Initialize the frame by loading the template.
	*/

	public function __construct()
	{
		fire(empty($this->sBaseTemplate));
		$this->oTpl = new weeTemplate($this->sBaseTemplate);
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

		fire(!method_exists($this, $sFunc));//TODO:404 error?
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

		@param	$mName	Name of the variable inside the template
		@param	$mValue	Value of the variable
		@see weeTemplate::set for details
	*/

	public function set($mName, $mValue)
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
/*		if ($this->sContext == 'xmlhttprequest')
		{
			header('Content-Type: text/xml');

			if (empty($this->oTaconite))
				return '<root></root>';
			return $this->oTaconite->toString();
		}

		if (!empty($this->oTaconite))
			return $this->oTaconite->applyTo($this->oTpl);
*/
		return $this->oTpl->toString();
	}

	/**
		Update the page using the specified rule.

		@warning Not tested yet.
	*/

/*	public function update($sMethod, $sWhere, $sWith)
	{
		if (empty($this->oTaconite))
			$this->oTaconite = weeTaconite::create();

		$this->oTaconite->addTag($sMethod, $sWhere, $sWith);
	}*/
}

?>
