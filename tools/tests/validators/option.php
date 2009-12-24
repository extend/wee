<?php

class CastableInput_testOptionValidator {
	public function __toString() {
		return '42';
	}
}

$oWidget = simplexml_load_string('<widget type="choice"/>');
$oHelper = new weeFormOptionsHelper($oWidget);

$o = new weeOptionValidator;

// weeOptionValidator::setValue should throw an DomainException when the value
// is not a scalar, an instance of Printable or an object castable to string.

try {
	$o->setValue(null);
} catch (DomainException $e) {
	$this->fail(_WT('weeOptionValidator::setValue should not throw a DomainException when the value is null.'));
}

try {
	$o->setValue(array());
	$this->fail(_WT('weeOptionValidator::setValue should throw a DomainException when the value is an array.'));
} catch (DomainException $e) {}

try {
	$o->setValue(true);
	$this->fail(_WT('weeOptionValidator::setValue should throw a DomainException when the value is a boolean.'));
} catch (DomainException $e) {}

try {
	$o->setValue(new stdClass);
	$this->fail(_WT('weeOptionValidator::setValue should throw a DomainException when the value is an object which is not string-compatible.'));
} catch (DomainException $e) {}

try {
	$o->setValue(new weeDummyPrintable('42'));
} catch (DomainException $e) {
	$this->fail(_WT('weeOptionValidator::setValue should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	$o->setValue(new CastableInput_testOptionValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeOptionValidator::setValue should not throw a DomainException when the value is an object castable to string.'));
}

try {
	$o->setValue(42);
} catch (DomainException $e) {
	$this->fail(_WT('weeOptionValidator::setValue should not throw a DomainException when the value is an integer.'));
}

try {
	$o->setValue(42.42);
} catch (DomainException $e) {
	$this->fail(_WT('weeOptionValidator::setValue should not throw a DomainException when the value is a float.'));
}

try {
	$o->setValue('42');
} catch (DomainException $e) {
	$this->fail(_WT('weeOptionValidator::setValue should not throw a DomainException when the value is a string.'));
}
// The following validation should fail.

$o->setFormData($oWidget, array());

$this->isTrue($o->hasError(),
	_WT('weeOptionValidator::hasError should return true when the value is not present in the widget options.'));

// The following validation should succeed.

$o = new weeOptionValidator;
$oHelper->addOption(42);
$o->setValue(42)->setFormData($oWidget, array());

$this->isFalse($o->hasError(),
	_WT('weeOptionValidator::hasError should return false when the value is present in the widget options.'));
