<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('mini', weeForm::ACTION_UPD);
$aPost = array(
	'hidden'	=> 42,
	'textbox'	=> 'error',
);

try {
	$oForm->validate($aPost);
	$this->fail('Form validation should have failed.');
} catch (FormValidationException $e) {
}
