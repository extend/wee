<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('required');

try {
	$oForm->validate(array());
	$this->fail(_WT('Form validation should have failed.'));
} catch (FormValidationException $e) {
}

try {
	$oForm->validate(array(
		'textbox' => 'testing',
	));
	$this->fail(_WT('Form validation should have failed.'));
} catch (FormValidationException $e) {
}

try {
	$oForm->validate(array(
		'checklist' => array(),
	));
	$this->fail(_WT('Form validation should have failed.'));
} catch (FormValidationException $e) {
}

try {
	$oForm->validate(array(
		'textbox' => 'testing',
		'checklist' => array(),
	));
	$this->fail(_WT('Form validation should have failed.'));
} catch (FormValidationException $e) {
}

try {
	$oForm->validate(array(
		'textbox' => 'testing',
		'checklist' => array(0 => 0),
	));
} catch (FormValidationException $e) {
	$this->fail(_WT('Form validation should not have failed.'));
}

try {
	$oForm->validate(array(
		'textbox' => 'testing',
		'checklist' => array(0 => 2),
	));
} catch (FormValidationException $e) {
	$this->fail(_WT('Form validation should not have failed.'));
}
