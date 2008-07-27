<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('mini');

$this->isEqual(2, sizeof($oForm->xpath('//widget')),
	'weeForm::xpath returned a wrong count of widgets.');
$this->isInstanceOf($oForm->xpathOne('//widget[@type="submitbutton"]'), 'SimpleXMLElement',
	'weeForm::xpathOne returned something unexpected.');

try {
	$oForm->xpathOne('//widget');
	$this->fail('weeForm::xpathOne should fail when finding more than 1 result.');
} catch (UnexpectedValueException $e) {
}
