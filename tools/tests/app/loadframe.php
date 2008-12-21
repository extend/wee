<?php

class weeApplication_testLoadFrame extends weeApplication
{
	// We need a public constructor.
	public function __construct() {}

	// We are testing this method, expose it.
	public function loadFrame($sFrame)
	{
		return parent::loadFrame($sFrame);
	}
}

class loadframe_test extends weeFrame
{
}

$o = new weeApplication_testLoadFrame;

try {
	$oFrame = $o->loadFrame('loadframe_test');
} catch (UnexpectedValueException $e) {
	$this->fail(_WT('loadFrame should not throws an UnexpectedValueException, the requested frame is valid.'));
}

$this->isTrue(is_object($oFrame),
	sprintf(_WT('loadFrame should return an object, got a %s variable instead.'), gettype($oFrame)));

$this->isInstanceOf($oFrame, 'weeFrame',
	sprintf(_WT('loadFrame should return a weeFrame instance, got a %s instance instead'), get_class($oFrame)));

$this->isInstanceOf($oFrame, 'loadframe_test',
	sprintf(_WT('loadFrame should return a loadframe_test instance, got a %s instance instead.'), get_class($oFrame)));

try {
	$o->loadFrame('frame_which_does_not_exist');
	$this->fail(_WT('loadFrame should throw a RouteNotFoundException, the requested frame does not exist.'));
} catch (RouteNotFoundException $e) {}

try {
	$o->loadFrame('stdClass');
	$this->fail(_WT('loadFrame should throw an RouteNotFoundException, the frame class is not a subclass of weeFrame'));
} catch (RouteNotFoundException $e) {}
