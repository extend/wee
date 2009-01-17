<?php

class PrintableInput_testConfirmValidator implements Printable {
	public function toString() {
		return '42';
	}
}

class CastableInput_testConfirmValidator {
	public function __toString() {
		return '42';
	}
}

// weeConfirmValidator should throw an InvalidArgumentException if the `with` argument is missing.

try {
	new weeConfirmValidator;
	$this->fail(_WT('weeConfirmValidator should throw an InvalidArgumentException when the `with` argument is missing.'));
} catch (InvalidArgumentException $e) {}

try {
	$o = new weeConfirmValidator(array('with' => 'confirm'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeConfirmValidator should not throw an InvalidArgumentException when the `with` argument is present.'));
}

// weeConfirmValidator::setValue should throw an DomainException when the value
// is not a scalar, an instance of Printable or an object castable to string.

try {
	$o->setValue(null);
	$this->fail(_WT('weeConfirmValidator::setValue should throw a DomainException when the value is null.'));
} catch (DomainException $e) {}

try {
	$o->setValue(array());
	$this->fail(_WT('weeConfirmValidator::setValue should throw a DomainException when the value is an array.'));
} catch (DomainException $e) {}

try {
	$o->setValue(new stdClass);
	$this->fail(_WT('weeConfirmValidator::setValue should throw a DomainException when the value is an object which is not string-compatible.'));
} catch (DomainException $e) {}

try {
	$o->setValue(new PrintableInput_testConfirmValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeConfirmValidator::setValue should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	$o->setValue(new CastableInput_testConfirmValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeConfirmValidator::setValue should not throw a DomainException when the value is an object castable to string.'));
}

try {
	$o->setValue(true);
} catch (DomainException $e) {
	$this->fail(_WT('weeConfirmValidator::setValue should not throw a DomainException when the value is a boolean.'));
}

try {
	$o->setValue(42);
} catch (DomainException $e) {
	$this->fail(_WT('weeConfirmValidator::setValue should not throw a DomainException when the value is an integer.'));
}

try {
	$o->setValue(42.42);
} catch (DomainException $e) {
	$this->fail(_WT('weeConfirmValidator::setValue should not throw a DomainException when the value is a float.'));
}

try {
	$o->setValue('42');
} catch (DomainException $e) {
	$this->fail(_WT('weeConfirmValidator::setValue should not throw a DomainException when the value is a string.'));
}

// The following validation should succeed.

$o->setFormData(simplexml_load_string('<widget/>'), array('confirm' => 42));

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
