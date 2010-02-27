<?php

/**
	Welcome page.

	Help the user in setting up the framework and run examples.
*/

class toppage extends weeFrame
{
	protected function defaultEvent($aEvent)
	{
		if (!is_writable(ROOT_PATH . 'app/tmp'))
			$this->set('error_tmp', true);

		try {
			weeApp()->db;
		} catch (DatabaseException $e) {
			$this->set('error_db', true);
		}
	}

	protected function eventInitDB($aEvent)
	{
		$this->runSQLScript(ROOT_PATH . 'app/sql/pastebin.sql');
		safe_header('Location: ' . APP_PATH);
	}

	protected function runSQLScript($sFilename)
	{
		$aQueries = explode(';', file_get_contents($sFilename));
		$iCount = count($aQueries) - 1;

		try {
			for ($i = 0; $i < $iCount; $i++)
				weeApp()->db->query($aQueries[$i]);
		} catch (DatabaseException $e) {
		}
	}
}
