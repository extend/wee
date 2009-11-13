<?php

class myFormUIExample extends weeContainerUI
{
	protected function defaultEvent($aEvent)
	{
		$oForm = new weeFormUI;
		$oForm->setParams(array('filename' => 'myform'));
		$oForm->setCallbacks(array(
			'setup'  => array($this, 'onSetup'),
			'submit' => array($this, 'onSubmit'),
		));
		$this->addFrame('form', $oForm);

		parent::defaultEvent($aEvent);
	}

	public function onSetup($aEvent, $oForm, $sAction)
	{
		// setup the form here if needed
	}

	public function onSubmit($aData)
	{
		saveIt($aData);
	}
}
