<?php

class weeApplication_testDispatch extends weeApplication
{
	// Override the default constructor to avoid the need of a config file.
	public function __construct()
	{
	}
}

class test_frame extends weeFrame
{
	// We need to expose it to test it.
	public $sContext;

	protected function defaultEvent($aEvent)
	{
	}

	protected function eventBurnInSetup($aEvent)
	{
	}

	protected function eventSentUnauthorizedEvent()
	{
		$this->sendEvent(array('name' => 'getOut'));
	}

	protected function eventGetOut($aEvent)
	{
		burn('UnauthorizedAccessException');
	}

	protected function setup($aEvent)
	{
		if (!strcasecmp(array_value($aEvent, 'name'), 'burnInSetup'))
			burn('UnauthorizedAccessException');
	}
}

$o = new weeApplication_testDispatch;

try
{
	$o->dispatchEvent(array('frame' => 'invalid_frame'));
	$this->fail(_('weeApplication does not throw an UnexpectedValueException upon invalid frame dispatch.'));
}
catch (UnexpectedValueException $e)
{
}

try
{
	$o->dispatchEvent(array('frame' => 'test_frame', 'name' => 'invalid'));
	$this->fail(_('weeApplication does not throw an UnexpectedValueException upon invalid frame/event dispatch.'));
}
catch (UnexpectedValueException $e)
{
}

try
{
	$o->dispatchEvent(array('frame' => 'test_frame'));
	$this->isTrue($o->getFrame() instanceof test_frame,
		sprintf(_('The application frame is an instance of class "%s" instead of "%s".'), get_class($o->getFrame()), 'test_frame'));
}
catch (UnexpectedValueException $e)
{
	$this->fail(_('weeApplication throws an UnexpectedValueException upon valid frame dispatch.'));
}

function frame_status($iStatus)
{
	static $aStatus = array(
		weeFrame::UNAUTHORIZED_ACCESS	=> 'UNAUTHORIZED_ACCESS',
		weeFrame::EVENT_DISPATCHED		=> 'EVENT_DISPATCHED'
	);

	return array_value($aStatus, $iStatus, 'undefined');
}

$o->dispatchEvent(array('frame' => 'test_frame'));
$this->isEqual($o->getFrame()->getStatus(), weeFrame::EVENT_DISPATCHED,
	sprintf(_('frame status is "%s" instead of "%s".'), frame_status($o->getFrame()->getStatus()), frame_status(weeFrame::EVENT_DISPATCHED)));

try
{
	$o->dispatchEvent(array('frame' => 'test_frame', 'name' => 'getOut'));
	$this->isEqual($o->getFrame()->getStatus(), weeFrame::UNAUTHORIZED_ACCESS,
		sprintf(_('frame status is "%s" instead of "%s".'), frame_status($o->getFrame()->getStatus()), frame_status(weeFrame::UNAUTHORIZED_ACCESS)));
}
catch (UnauthorizedAccessException $e)
{
	$this->fail(_('An UnauthorizedAccessException has been left uncaught.'));
}

try
{
	$o->dispatchEvent(array('frame' => 'test_frame', 'name' => 'burnInSetup'));
	$this->isEqual($o->getFrame()->getStatus(), weeFrame::UNAUTHORIZED_ACCESS,
		sprintf(_('frame status is "%s" instead of "%s".'), frame_status($o->getFrame()->getStatus()), frame_status(weeFrame::UNAUTHORIZED_ACCESS)));
}
catch (UnauthorizedAccessException $e)
{
	$this->fail(_('An UnauthorizedAccessException thrown from the frame setup() method has been left uncaught.'));
}

$o->dispatchEvent(array('frame' => 'test_frame', 'context' => 'xmlhttprequest'));
$this->isEqual($o->getFrame()->sContext, 'xmlhttprequest',
	_('The context is not correctly propagated to the frame.'));

try {
	$o->dispatchEvent(array('frame' => 'test_frame', 'name' => 'sentUnauthorizedEvent'));
	$this->fail(_('An IllegalStateException should be thrown when a nested event throws an UnauthorizedAccessException.'));
} catch (IllegalStateException $e) {}
