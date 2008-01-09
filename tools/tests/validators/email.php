<?php

class validators_email
{
	public function __toString()
	{
		return 'valid@email.com';
	}
}

// Valid

$this->isTrue(weeEmailValidator::test('test@example.com'),
	'weeEmailValidator fails to validate "test@example.com".');
$this->isTrue(weeEmailValidator::test('test.test@example.com'),
	'weeEmailValidator fails to validate "test.test@example.com".');
$this->isTrue(weeEmailValidator::test(new validators_email),
	'weeEmailValidator fails to validate an object that returns "valid@email.com" as a string.');

// Invalid

$this->isFalse(weeEmailValidator::test(null),
	'weeEmailValidator returns true for a null value.');
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
$this->isFalse(weeEmailValidator::test('test@com.example'),
	'weeEmailValidator returns true for "test@com.example".');
$this->isFalse(weeEmailValidator::test('test@@example.com'),
	'weeEmailValidator returns true for "test@@example.com".');
$this->isFalse(weeEmailValidator::test('test@test@example.com'),
	'weeEmailValidator returns true for "test@test@example.com".');
$this->isFalse(weeEmailValidator::test(new stdClass),
	'weeEmailValidator returns true for an empty object.');

// Other types

$sInvalidTypeError = 'weeEmailValidator returns true for an invalid type.';

$this->isFalse(weeEmailValidator::test(0), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(1), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(-1), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(111), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(-111), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(20000000), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(1.0), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(1.1), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(true), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(false), $sInvalidTypeError);
$this->isFalse(weeEmailValidator::test(array(1, 2, 3, 'test', false)), $sInvalidTypeError);
