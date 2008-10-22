<?php

class weeApplication_testEventCached extends weeApplication
{
	// We need a public constructor.
	public function __construct() {}

	// We are testing this method, expose it.
	public function isEventCached(&$aEvent)
	{
		return parent::isEventCached($aEvent);
	}
}

define('CACHE_PATH', ROOT_PATH . 'app/tmp/appcache/');

$oTestEventCached = new weeApplication_testEventCached();

$aEvent['frame']	= 'frame_which_does_not_exist';
$sCacheFilename		= CACHE_PATH . $aEvent['frame'] . '/?';

$this->isFalse($oTestEventCached->isEventCached($aEvent),
	sprintf(_('The frame %s does not exist.'), $sCacheFilename));

$aEvent['frame']	= 'cache';
$sCacheFilename		= CACHE_PATH . $aEvent['frame'] . '/?';

$iRet = @mkdir(CACHE_PATH . $aEvent['frame'], 0755, true);
$iRet === false and burn('UnexpectedValueException', sprintf(_('Cannot create (or recreate) the directory %s.'), CACHE_PATH . $aEvent['frane']));

touch($sCacheFilename, time() - 5);
clearstatcache();

$this->isFalse($oTestEventCached->isEventCached($aEvent),
	sprintf(_('The page %s has expired.'), $sCacheFilename));

touch($sCacheFilename, time() + 42);
clearstatcache();

$this->isTrue($oTestEventCached->isEventCached($aEvent),
	sprintf(_('The page %s should not have expired.'), $sCacheFilename));
