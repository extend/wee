<?php

class myExample extends weeFrame
{
	// No need to specify the template: it is the name of the class by default
	// In other words it is set to 'mytemplate' here

	protected function defaultEvent($aEvent)
	{
		$this->set('name', 'World');
		$this->set('items', array('earth', 'moon', 'mars'));
	}
}
