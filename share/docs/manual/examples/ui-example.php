<?php

class example extends weeContainerUI
{
	protected function setup($aEvent)
	{
		parent::setup($aEvent);

		$this->setTemplate('my_example');

		$this->addFrame('breadcrumbs', new weeBreadcrumbsUI);

		$oCRUD = new weeCRUDUI($this->oController);
		$oCRUD->setParams(array('set' => 'myExamplesSet'));
		$this->addFrame('crud', $oCRUD);
	}
}
