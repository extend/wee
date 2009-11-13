<?php

class myFrame extends weeFrame {
	protected function defaultEvent($aEvent)
	{
		// Send the event to this frame's test event
		$this->sendEvent(array('name' => 'test') + $aEvent);

		// Same, but bypassing the authorization test
		// Changing 'name' in $aEvent is not mandatory in this case
		$this->eventTest($aEvent);
	}

	protected function eventTest($aEvent)
	{
		// Event stuff here
	}
}
