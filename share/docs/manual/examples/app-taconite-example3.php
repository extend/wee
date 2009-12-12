<?php

class example3 extends weeFrame
{
	protected function eventEdit($aEvent)
	{
		$oResource = exResourceSet::instance()->fetch($aEvent['get']['r']);

		$oForm = new weeForm('resource', 'edit');
		$oForm->fill($oResource);

		if (isset($aEvent['post'])) {
			$aData = $oForm->filter($aEvent['post']);

			try {
				$oForm->validate($aData);

				$oResource->set($aData);
				$oResource->update();

				$this->update('replaceContent', '#msg', 'The resource has been successfully edited!');
			} catch (FormValidationException $e) {
				$this->update('replaceContent', '#msg', 'The submitted data is erroneous!');
				$oForm->fillErrors($e);
			}

			$oForm->fill($aData);

			if (array_value($aEvent, 'context') == 'xmlhttprequest')
				$this->update('replace', 'form', $oForm);
		}

		$this->set('form', $oForm);
	}
}
