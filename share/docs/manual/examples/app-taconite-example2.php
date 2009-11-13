<?php

class example2 extends weeFrame
{
	protected function defaultEvent($aEvent)
	{
		if ($aEvent['context'] == 'xmlhttprequest') {
			$oTpl = new weeTemplate('the_date', array('date' => date()));
			$this->update('replaceContent', '#date', $oTpl->toString());
		} else {
			$this->set('date', date());
		}
	}
}
