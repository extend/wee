<?php

class myFrame extends weeFrame
{
	// ...

	protected function defaultEvent($aEvent)
	{
		$oForm = new weeForm('myform', 'update');

		if (!empty($aEvent['post'])) {
			$aData = $oForm->filter($aEvent['post']);

			try {
				$oForm->validate($aData);
				// Validation success: process the form
				doSomething($aData);
			} catch (FormValidationException $e) {
				$oForm->fill($aData);
				$oForm->fillErrors($e);
			}
		}

		$this->set('form', $oForm);
	}
}
