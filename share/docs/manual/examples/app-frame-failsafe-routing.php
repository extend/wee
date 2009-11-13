<?php

class myFrame extends weeFrame implements weeFailSafeRouting
{
	protected defaultEvent($aEvent)
	{
		doSomething();
	}

	protected eventSave($aEvent)
	{
		doSave();
	}
}
