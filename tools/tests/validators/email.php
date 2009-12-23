<?php

class CastableInput_testEmailValidator {
	public function __toString() {
		return 'valid@email.com';
	}
}

// weeEmailValidator should throw a DomainException when the value to validate is neither a string, an instance of Printable or an object castable to string.

$o = new weeEmailValidator;

try {
	$o->setValue(new stdClass);
	$this->fail(_WT('weeEmailValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
} catch (DomainException $e) {}

try {
	$o->setValue(true);
	$this->fail(_WT('weeEmailValidator should throw a DomainException when the value is a boolean.'));
} catch (DomainException $e) {}

try {
	$o->setValue(null);
	$this->fail(_WT('weeEmailValidator should throw a DomainException when the value is null.'));
} catch (DomainException $e) {}

try {
	$o->setValue(array());
	$this->fail(_WT('weeEmailValidator should throw a DomainException when the value is an array.'));
} catch (DomainException $e) {}

try {
	$o->setValue(42);
	$this->fail(_WT('weeEmailValidator should throw a DomainException when the value is an integer.'));
} catch (DomainException $e) {}

try {
	$o->setValue(42.42);
	$this->fail(_WT('weeEmailValidator should throw a DomainException when the value is a float.'));
} catch (DomainException $e) {}


try {
	$o->setValue('valid@email.com');
} catch (DomainException $e) {
	$this->fail(_WT('weeEmailValidator should not throw a DomainException when the value is a string.'));
}

try {
	$o->setValue(new weeDummyPrintable('valid@email.com'));
} catch (DomainException $e) {
	$this->fail(_WT('weeEmailValidator should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	$o->setValue(new CastableInput_testEmailValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeEmailValidator should not throw a DomainException when the value is an object castable to string.'));
}

// Valid

$this->isTrue(weeEmailValidator::test('test@example.com'),
	_WT('weeEmailValidator fails to validate "test@example.com".'));
$this->isTrue(weeEmailValidator::test('test.test@example.com'),
	_WT('weeEmailValidator fails to validate "test.test@example.com".'));

// Invalid

$this->isFalse(weeEmailValidator::test(''),
	_WT('weeEmailValidator returns true for the empty string.'));
$this->isFalse(weeEmailValidator::test('example'),
	_WT('weeEmailValidator returns true for "example".'));
$this->isFalse(weeEmailValidator::test('example.com'),
	_WT('weeEmailValidator returns true for "example.com".'));
$this->isFalse(weeEmailValidator::test('@example.com'),
	_WT('weeEmailValidator returns true for "@example.com".'));
$this->isFalse(weeEmailValidator::test('test@example'),
	_WT('weeEmailValidator returns true for "test@example".'));
$this->isFalse(weeEmailValidator::test('test.test@example'),
	_WT('weeEmailValidator returns true for "test.test@example".'));
$this->isFalse(weeEmailValidator::test('test@@example.com'),
	_WT('weeEmailValidator returns true for "test@@example.com".'));
$this->isFalse(weeEmailValidator::test('test@test@example.com'),
	_WT('weeEmailValidator returns true for "test@test@example.com".'));

// Objects

$this->isTrue(weeEmailValidator::test(new weeDummyPrintable('valid@email.com')),
	_WT('weeEmailValidator::test should return true when the value is an instance of Printable which returns a valid number.'));

$this->isTrue(weeEmailValidator::test(new CastableInput_testEmailValidator),
	_WT('weeEmailValidator::test should return true when the value is an object castable to string which casts to a valid number.'));
