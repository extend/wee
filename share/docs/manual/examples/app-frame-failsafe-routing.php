<?php

class myFrame extends weeFrame implements weeFailSafeRouting
{
	// ...

	protected function defaultEvent($aEvent)
	{
		doSomething();
	}

	protected function eventSave($aEvent)
	{
		doSave();
	}
}
