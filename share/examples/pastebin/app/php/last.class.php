<?php

class last extends weeFrame
{
	protected function defaultEvent($aEvent)
	{
		$this->set('pastebins', weeApp()->db->query('
			SELECT *
				FROM pastebin_data
				ORDER BY data_id DESC
				LIMIT 5
		'));
	}
}
