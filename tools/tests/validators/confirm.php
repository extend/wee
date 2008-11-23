<?php

// weeConfirmValidator should throw an InvalidArgumentException if the `with` argument is missing.

try {
	new weeConfirmValidator;
	$this->fail(_WT('weeConfirmValidator should throw an InvalidArgumentException when the `with` argument is missing.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeConfirmValidator(array('with' => 'confirm'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeConfirmValidator should not throw an InvalidArgumentException when the `with` argument is present.'));
}

// The following validation should succeed.

$o = new weeConfirmValidator(array('with' => 'confirm'));
$o->setValue('42')->setFormData(simplexml_load_string('<widget/>'), array('confirm' => 42));

$this->isFalse($o->hasError(),
	_WT('weeConfirmValidator::hasError should return false when the value is confirmed in the form data.'));

// The following validations should fail.

$o = new weeConfirmValidator(array('with' => 'confirm'));
$o->setValue('42')->setFormData(simplexml_load_string('<widget/>'), array('confirm' => 43));

$this->isTrue($o->hasError(),
	_WT('weeConfirmValidator::hasError should return true when the value is not confirmed in the form data.'));

$o = new weeConfirmValidator(array('with' => 'confirm'));
$o->setValue('42')->setFormData(simplexml_load_string('<widget/>'), array());

$this->isTrue($o->hasError(),
	_WT('weeConfirmValidator::hasError should return true when the confirmation value is missing from the form data.'));
