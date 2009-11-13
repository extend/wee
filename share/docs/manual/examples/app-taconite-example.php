<?php

class example extends weeFrame
{
	protected function defaultEvent($aEvent)
	{
		if ($aEvent['context'] == 'xmlhttprequest')
			$this->update('replaceContent', '#date', date());
		else
			$this->set('date', date());
	}
}
