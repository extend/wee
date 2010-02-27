<?php

/**
	Simple pastebin example.

	URLs are of the form /pastebin for the main page and /pastebin/123 for the individual pages.
	Routing (configured in app/conf/wee.cnf) translates the second into /pastebin?id=123.

	Forgive the huge amount of comments in this example, this is destined to beginners.
*/

class pastebin extends weeFrame implements weeFailSafeRouting
{
	protected function defaultEvent($aEvent)
	{
		// Create our pastebin set object. See the file pastebinSet.class.php.
		$oSet = new pastebinSet;

		// Always send the last 10 inserted pastebins to the template.
		$this->set('last_pastebins', $oSet->orderBy(array('pastebin_timestamp' => 'DESC'))->fetchSubset(0, 10));

		// Always make sure the input data is what you expect before using it.
		// Here ctype_digit ensures $aEvent['get']['id'] is a number.
		if (!empty($aEvent['get']['id']) && ctype_digit($aEvent['get']['id'])) {
			// Send the requested pastebin to the template.
			// Also no need to show the form, so return here.
			return $this->set('pastebin', $oSet->fetch($aEvent['get']['id']));
		}

		// Create the pastebin form and send it to the template.
		// It's OK to send it here because objects are always references,
		// so the template will have all the subsequent modifications we make to the object.
		$oForm = new weeForm('pastebin');
		$this->set('form', $oForm);

		// Validate the data sent by the form, if any.
		// The form_validate function is a shorthand from wee/weexlib.php.
		if (!empty($aEvent['post']) && form_validate($oForm, $aEvent['post'])) {
			// form_validate checks and filter everything in $aEvent['post'].
			// This means everything in it is safe to use,
			// as long as the form definition file is thorough.
			$oPastebin = $oSet->insert($aEvent['post']);

			// Send the inserted pastebin to the template.
			$this->set(array(
				'success' => true,
				'pastebin' => $oPastebin,
			));
		}
	}

	/**
		This event sends the requested pastebin as a text file instead of outputting it to the browser.
	*/

	protected function eventDownload($aEvent)
	{
		ctype_digit($aEvent['get']['id']) or burn('UnexpectedValueException',
			_T('The requested pastebin ID is invalid.'));

		// After checking that the ID is a number, we fetch the pastebin.
		$oSet = new pastebinSet;
		$oPastebin = $oSet->fetch($aEvent['get']['id']);

		// Then we change the template to one that will only output the pastebin text.
		$this->sBaseTemplate = 'pastebin_download';
		// We also change the encoding settings from XHTML (defaults) to plain text.
		$this->getRenderer()->setEncoder(new weeTextEncoder);
		// Then we tell the application object to serve this output as a file.
		weeApp()->serveAsFile(sprintf('pastebin_%d.txt', $aEvent['get']['id']));

		// Let's not forget to send the pastebin data to the template, of course.
		$this->set('pastebin', $oPastebin);
	}
}
