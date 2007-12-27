<?php

class view extends weeFrame
{
	protected function defaultEvent($aEvent)
	{
		if (empty($aEvent['get']['id']) || !ctype_digit($aEvent['get']['id']))
			return $this->set('error', 'Missing or invalid id parameter.');

		$oResults = weeApp()->db->query('
			SELECT *
				FROM pastebin_data
				WHERE data_id=:id
				LIMIT 1
		', $aEvent['get']);

		if (count($oResults) != 1)
			return $this->set('error', 'Pasted data not found for this id.');

		$this->set('paste', $oResults->fetch());
	}
}
