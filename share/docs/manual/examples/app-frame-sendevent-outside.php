<?php

class frameA extends weeFrame {
	protected function defaultEvent($aEvent)
	{
		// Send the event to frameB
		$this->sendEvent(array('frame' => 'frameB', 'noframeschange' => 1) + $aEvent);
	}
}

class frameB extends weeFrame {
	protected function defaultEvent($aEvent)
	{
		// Event stuff here
	}
}
