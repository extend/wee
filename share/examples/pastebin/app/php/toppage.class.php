<?php

class toppage extends weeFrame
{
	protected function defaultEvent($aEvent)
	{
		$oForm = new weeForm('pastebin');
		$this->set(array(
			'form' => $oForm,
			'is_submitted' => !empty($aEvent['post']),
		));

		if (!empty($aEvent['post'])) {
			try {
				$aEvent['post'] = $oForm->filter($aEvent['post']);
				$oForm->validate($aEvent['post']);

				weeApp()->db->query('
					INSERT INTO pastebin_data (data_text) VALUES (:data_text)
				', $aEvent['post']);

				$this->set('posted_id', weeApp()->db->getPKId('pastebin_data_data_id_seq'));
			} catch (FormValidationException $e) {
				$this->set('errors', $e->toArray());
			}
		}
	}
}
