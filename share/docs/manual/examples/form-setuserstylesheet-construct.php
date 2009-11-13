<?php

class myForm extends weeForm
{
	public function __construct($sFilename, $sAction = 'add')
	{
		$this->setUserStylesheetsPath(ROOT_PATH . 'app/xsl');
		parent::__construct($sFilename, $sAction);
	}
}
