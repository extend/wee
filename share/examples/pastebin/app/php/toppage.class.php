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

		if (!empty($aEvent['post']))
		{
			if ($oForm->hasErrors($aEvent['post']))
				$this->set('errors', $oForm->getErrors());
			else
			{
				weeApp()->db->query($oForm->toSQL($aEvent['post'], 'pastebin_data'));
				$this->set('posted_id', weeApp()->db->getPKId('pastebin_data_data_id_seq'));
			}
		}
	}
}
