<?php

protected function defaultEvent($aEvent)
{
	$oListUI = new weeListUI($this->oController);
	init($oListUI);
	$this->addFrame('index', $oListUI);

	runEvent();
}
