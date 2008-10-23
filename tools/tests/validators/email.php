<?php

class PrintableInput_testEmailValidator implements Printable {
	public function toString() {
		return 'valid@email.com';
	}
}

class CastableInput_testEmailValidator {
	public function __toString() {
		return 'valid@email.com';
	}
}

// weeEmailValidator should throw a DomainException when the value to validate is neither a string, an instance of Printable or an object castable to string.

try {
	new weeEmailValidator(new stdClass);
	$this->fail(_('weeEmailValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
} catch (DomainException $e) {}

try {
	new weeEmailValidator(true);
	$this->fail(_('weeEmailValidator should throw a DomainException when the value is a boolean.'));
} catch (DomainException $e) {}

try {
	new weeEmailValidator(null);
	$this->fail(_('weeEmailValidator should throw a DomainException when the value is null.'));
} catch (DomainException $e) {}

try {
	new weeEmailValidator(array());
	$this->fail(_('weeEmailValidator should throw a DomainException when the value is an array.'));
} catch (DomainException $e) {}

try {
	new weeEmailValidator(42);
	$this->fail(_('weeEmailValidator should throw a DomainException when the value is an integer.'));
} catch (DomainException $e) {}

try {
	new weeEmailValidator(42.42);
	$this->fail(_('weeEmailValidator should throw a DomainException when the value is a float.'));
} catch (DomainException $e) {}


try {
	new weeEmailValidator('valid@email.com');
} catch (DomainException $e) {
	$this->fail(_('weeEmailValidator should not throw a DomainException when the value is a string.'));
}

try {
	new weeEmailValidator(new PrintableInput_testEmailValidator);
} catch (DomainException $e) {
	$this->fail(_('weeEmailValidator should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	new weeEmailValidator(new CastableInput_testEmailValidator);
} catch (DomainException $e) {
	$this->fail(_('weeEmailValidator should not throw a DomainException when the value is an object castable to string.'));
}

// Valid

$this->isTrue(weeEmailValidator::test('test@example.com'),
	'weeEmailValidator fails to validate "test@example.com".');
$this->isTrue(weeEmailValidator::test('test.test@example.com'),
	'weeEmailValidator fails to validate "test.test@example.com".');

// Invalid

$this->isFalse(weeEmailValidator::test(''),
	'weeEmailValidator returns true for the empty string.');
$this->isFalse(weeEmailValidator::test('example'),
	'weeEmailValidator returns true for "example".');
$this->isFalse(weeEmailValidator::test('example.com'),
	'weeEmailValidator returns true for "example.com".');
$this->isFalse(weeEmailValidator::test('@example.com'),
	'weeEmailValidator returns true for "@example.com".');
$this->isFalse(weeEmailValidator::test('test@example'),
	'weeEmailValidator returns true for "test@example".');
$this->isFalse(weeEmailValidator::test('test@@example.com'),
	'weeEmailValidator returns true for "test@@example.com".');
$this->isFalse(weeEmailValidator::test('test@test@example.com'),
	'weeEmailValidator returns true for "test@test@example.com".');

// Objects

$this->isTrue(weeEmailValidator::test(new PrintableInput_testEmailValidator),
	_('weeEmailValidator::test should return true when the value is an instance of Printable which returns a valid number.'));

$this->isTrue(weeEmailValidator::test(new CastableInput_testEmailValidator),
	_('weeEmailValidator::test should return true when the value is an object castable to string which casts to a valid number.'));
