<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('mini', weeForm::ACTION_ADD);
$this->isEqual(2, sizeof($oForm->xpath('//widget')),
	'weeForm::xpath returned a wrong count of widgets.');

$oForm = new weeForm('mini', weeForm::ACTION_UPD);
$this->isEqual(3, sizeof($oForm->xpath('//widget')),
	'weeForm::xpath returned a wrong count of widgets.');
