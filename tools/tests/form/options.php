<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('options', weeForm::ACTION_UPD);
$oHelper = $oForm->helper('weeFormOptionsHelper', 'choice');

$oHelper->addOptions(array(
	array('label' => 'test 1', 'value' => '39'),
	array('label' => 'test 2', 'value' => '40'),
	array('label' => 'test 3', 'value' => '41'),
	array('label' => 'test 4', 'value' => '43'),
	array('label' => 'test 5', 'value' => '44'),
));

try {
	$oForm->validate(array('choice' => 42));
	$this->fail('Form validation should have failed.');
} catch (FormValidationException $e) {
}

$oHelper->addOption(array('label' => 'test 42', 'value' => '42'));

try {
	$oForm->validate(array('choice' => 42));
} catch (FormValidationException $e) {
	$this->fail('Form validation should have succeeded.');
}

$oHelper->select('42');
$this->isFalse($oHelper->isSelected(39), 'Only 42 should have been selected.');
$this->isFalse($oHelper->isSelected(40), 'Only 42 should have been selected.');
$this->isFalse($oHelper->isSelected(41), 'Only 42 should have been selected.');
$this->isTrue($oHelper->isSelected(42), 'Only 42 should have been selected.');
$this->isFalse($oHelper->isSelected(43), 'Only 42 should have been selected.');
$this->isFalse($oHelper->isSelected(44), 'Only 42 should have been selected.');

$oHelper->selectNone();
$oHelper->select(array('41', '44'));
$this->isFalse($oHelper->isSelected(39), 'Only 41 and 44 should have been selected.');
$this->isFalse($oHelper->isSelected(40), 'Only 41 and 44 should have been selected.');
$this->isTrue($oHelper->isSelected(41), 'Only 41 and 44 should have been selected.');
$this->isFalse($oHelper->isSelected(42), 'Only 41 and 44 should have been selected.');
$this->isFalse($oHelper->isSelected(43), 'Only 41 and 44 should have been selected.');
$this->isTrue($oHelper->isSelected(44), 'Only 41 and 44 should have been selected.');

$oHelper->selectOne('43');
$this->isFalse($oHelper->isSelected(39), 'Only 43 should have been selected.');
$this->isFalse($oHelper->isSelected(40), 'Only 43 should have been selected.');
$this->isFalse($oHelper->isSelected(41), 'Only 43 should have been selected.');
$this->isFalse($oHelper->isSelected(42), 'Only 43 should have been selected.');
$this->isTrue($oHelper->isSelected(43), 'Only 43 should have been selected.');
$this->isFalse($oHelper->isSelected(44), 'Only 43 should have been selected.');
