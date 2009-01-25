<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('mini');
$oForm->setUserStylesheetsPath(dirname(__FILE__) . '/xslt/');

$this->isMatching('/Test/', $oForm->toString(),
	_WT("The user stylesheet for submitbutton isn't loaded properly."));
