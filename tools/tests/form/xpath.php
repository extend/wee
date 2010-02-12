<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('mini');

$this->isEqual(2, count($oForm->xpath('//widget')),
	_WT('weeForm::xpath returned a wrong count of widgets.'));
$this->isInstanceOf($oForm->xpathOne('//widget[@type="submitbutton"]'), 'SimpleXMLElement',
	_WT('weeForm::xpathOne returned something unexpected.'));

try {
	$oForm->xpathOne('//widget');
	$this->fail(_WT('weeForm::xpathOne should fail when finding more than 1 result.'));
} catch (UnexpectedValueException $e) {
}

$oForm->removeNodes('//widget[name="hidden"]');
$this->isEqual(0, count($oForm->xpath('//widget[name="hidden"]')),
	_WT('weeForm::removeNodes does not work.'));
