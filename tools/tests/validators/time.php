<?php

class PrintableInput_testTimeValidator implements Printable {
	public function toString() {
		return '09:42';
	}
}

class CastableInput_testTimeValidator {
	public function __toString() {
		return '09:42';
	}
}

$sFormerTimezone = @date_default_timezone_get();
date_default_timezone_set('Europe/Paris');

try {
	// weeTimeValidator should throw a DomainException when the value to validate is not a string or an instance of Printable or an object castable to string.

	$o = new weeTimeValidator;

	try {
		$o->setValue(new stdClass);
		$this->fail(_WT('weeTimeValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
	} catch (DomainException $e) {}

	try {
		$o->setValue(true);
		$this->fail(_WT('weeTimeValidator should throw a DomainException when the value is a boolean.'));
	} catch (DomainException $e) {}

	try {
		$o->setValue(null);
		$this->fail(_WT('weeTimeValidator should throw a DomainException when the value is null.'));
	} catch (DomainException $e) {}

	try {
		$o->setValue(array());
		$this->fail(_WT('weeTimeValidator should throw a DomainException when the value is an array.'));
	} catch (DomainException $e) {}

	try {
		$o->setValue(42);
		$this->fail(_WT('weeTimeValidator should throw a DomainException when the value is an integer.'));
	} catch (DomainException $e) {}

	try {
		$o->setValue(42.42);
		$this->fail(_WT('weeTimeValidator should throw a DomainException when the value is a float.'));
	} catch (DomainException $e) {}

	try {
		$o->setValue('09:42');
	} catch (DomainException $e) {
		$this->fail(_WT('weeTimeValidator should not throw a DomainException when the value is a string.'));
	}

	try {
		$o->setValue(new PrintableInput_testTimeValidator);
	} catch (DomainException $e) {
		$this->fail(_WT('weeTimeValidator should not throw a DomainException when the value is an instance of Printable.'));
	}

	try {
		$o->setValue(new CastableInput_testTimeValidator);
	} catch (DomainException $e) {
		$this->fail(_WT('weeTimeValidator should not throw a DomainException when the value is an object castable to string.'));
	}

	// The following validations should succeed.

	$this->isTrue(weeTimeValidator::test('09:42'),
		_WT('weeTimeValidator::test should return true when the value is a valid time.'));

	$this->isTrue(weeTimeValidator::test('21:42'),
		_WT('weeTimeValidator::test should return true when the value is a valid 24-hour time.'));

	$this->isTrue(weeTimeValidator::test(new PrintableInput_testTimeValidator),
		_WT('weeTimeValidator::test should return true when the value is an instance of Printable which returns a valid time.'));

	$this->isTrue(weeTimeValidator::test(new CastableInput_testTimeValidator),
		_WT('weeTimeValidator::test should return true when the value is an object castable to string which casts to a valid time.'));

	// The following validations should fail.

	$this->isFalse(weeTimeValidator::test('FAIL'),
		_WT('weeTimeValidator::test should return false when the value is not a valid time.'));

	$this->isFalse(weeTimeValidator::test('09h42'),
		_WT('weeTimeValidator::test should return false when the value is not a properly formatted time.'));
		
	$this->isFalse(weeTimeValidator::test('24:42'),
		_WT('weeTimeValidator::test should return false when the hour is equal to 24.'));

	$this->isFalse(weeTimeValidator::test('25:42'),
		_WT('weeTimeValidator::test should return false when the hour is greater than 24.'));

	$this->isFalse(weeTimeValidator::test('09:60'),
		_WT('weeTimeValidator::test should return false when the minute is equal to 60.'));

	$this->isFalse(weeTimeValidator::test('09:61'),
		_WT('weeTimeValidator::test should return false when the minute is greater than 60.'));
} catch (Exception $oException) {}

date_default_timezone_set($sFormerTimezone);
if (isset($oException))
	throw $oException;
