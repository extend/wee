<?php

class myFrame extends weeFrame {
	protected function defaultEvent($aEvent)
	{
		// Event stuff here
	}

	protected function setup($aEvent)
	{
		weeApp()->session['is_admin'] or burn('UnauthorizedAccessException',
			_T('You are not permitted to access this page.'));
	}

	protected function unauthorizedAccess($aEvent)
	{
		header('Location: http://example.org/login');
		exit;
	}
}
