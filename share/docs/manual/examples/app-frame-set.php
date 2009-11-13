<?php

class myFrame extends weeFrame {
	protected function defaultEvent($aEvent)
	{
		$this->set('nickname', 'essen');

		// You can also do this:
		$this->set(array(
			'title' => 'My Frame',
			'description' => 'This is an example frame.',
		));
	}
}
