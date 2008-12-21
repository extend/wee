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
	$this->fail(_WT('weeApplication does not throw a RouteNotFoundException upon invalid frame dispatch.'));
}
catch (RouteNotFoundException $e)
{
}

try
{
	$o->dispatchEvent(array('frame' => 'test_frame', 'name' => 'invalid'));
	$this->fail(_WT('weeApplication does not throw a RouteNotFoundException upon invalid frame/event dispatch.'));
}
catch (RouteNotFoundException $e)
{
}

try
{
	$o->dispatchEvent(array('frame' => 'test_frame'));

	$this->isTrue(is_object($o->getFrame()),
		_WT('weeApplication::getFrame should return an object.'));

	$this->isInstanceOf($o->getFrame(), 'test_frame',
		_WT('weeApplication::getFrame should return an instance of the frame class passed to weeApplication::dispatchEvent.'));
}
catch (RouteNotFoundException $e)
{
	$this->fail(_WT('weeApplication throws a RouteNotFoundException upon valid frame dispatch.'));
}

function frame_status($iStatus)
{
	static $aStatus = array(
		weeFrame::UNAUTHORIZED_ACCESS	=> 'weeFrame::UNAUTHORIZED_ACCESS',
		weeFrame::EVENT_DISPATCHED		=> 'weeFrame::EVENT_DISPATCHED'
	);

	return array_value($aStatus, $iStatus, 'undefined');
}

$o->dispatchEvent(array('frame' => 'test_frame'));
$this->isEqual(weeFrame::EVENT_DISPATCHED, $o->getFrame()->getStatus(),
	sprintf(_WT('weeFrame::getStatus should return weeFrame::EVENT_DISPATCHED instead of %s when the event has been successfully dispatched.'),
		frame_status($o->getFrame()->getStatus())));

try
{
	$o->dispatchEvent(array('frame' => 'test_frame', 'name' => 'getOut'));
	$this->isEqual(weeFrame::UNAUTHORIZED_ACCESS, $o->getFrame()->getStatus(),
	sprintf(_WT('weeFrame::getStatus should return weeFrame::UNAUTHORIZED_ACCESS instead of %s when the event method has thrown an UnauthorizedAccessException.'),
		frame_status($o->getFrame()->getStatus())));
}
catch (UnauthorizedAccessException $e)
{
	$this->fail(_WT('An UnauthorizedAccessException thrown from the event method has been left uncaught.'));
}

try
{
	$o->dispatchEvent(array('frame' => 'test_frame', 'name' => 'burnInSetup'));
	$this->isEqual(weeFrame::UNAUTHORIZED_ACCESS, $o->getFrame()->getStatus(),
	sprintf(_WT('weeFrame::getStatus should return weeFrame::UNAUTHORIZED_ACCESS instead of %s when the setup method has thrown an UnauthorizedAccessException.'),
		frame_status($o->getFrame()->getStatus())));
}
catch (UnauthorizedAccessException $e)
{
	$this->fail(_WT('An UnauthorizedAccessException thrown from the setup method has been left uncaught.'));
}

$o->dispatchEvent(array('frame' => 'test_frame', 'context' => 'xmlhttprequest'));
$this->isEqual('xmlhttprequest', $o->getFrame()->sContext,
	_WT('The context is not correctly propagated to the frame.'));

try {
	$o->dispatchEvent(array('frame' => 'test_frame', 'name' => 'sentUnauthorizedEvent'));
	$this->fail(_WT('An IllegalStateException should be thrown when a nested event throws an UnauthorizedAccessException.'));
} catch (IllegalStateException $e) {}
