<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('mini', 'add');
$this->isEqual(2, sizeof($oForm->xpath('//widget')),
	'weeForm::xpath returned a wrong count of widgets.');

$oForm = new weeForm('mini', 'update');
$this->isEqual(3, sizeof($oForm->xpath('//widget')),
	'weeForm::xpath returned a wrong count of widgets.');
