<?php

class myExample extends weeFrame
{
	protected function defaultEvent($aEvent)
	{
		$this->set(array(
			'name'  => 'World',
			'items' => array('earth', 'moon', 'mars'),
		));
	}
}
