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

	try {
		new weeTimeValidator(new stdClass);
		$this->fail(_('weeTimeValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
	} catch (DomainException $e) {}

	try {
		new weeTimeValidator(true);
		$this->fail(_('weeTimeValidator should throw a DomainException when the value is a boolean.'));
	} catch (DomainException $e) {}

	try {
		new weeTimeValidator(null);
		$this->fail(_('weeTimeValidator should throw a DomainException when the value is null.'));
	} catch (DomainException $e) {}

	try {
		new weeTimeValidator(array());
		$this->fail(_('weeTimeValidator should throw a DomainException when the value is an array.'));
	} catch (DomainException $e) {}

	try {
		new weeTimeValidator(42);
		$this->fail(_('weeTimeValidator should throw a DomainException when the value is an integer.'));
	} catch (DomainException $e) {}

	try {
		new weeTimeValidator(42.42);
		$this->fail(_('weeTimeValidator should throw a DomainException when the value is a float.'));
	} catch (DomainException $e) {}

	try {
		new weeTimeValidator('09:42');
	} catch (DomainException $e) {
		$this->fail(_('weeTimeValidator should not throw a DomainException when the value is a string.'));
	}

	try {
		new weeTimeValidator(new PrintableInput_testTimeValidator);
	} catch (DomainException $e) {
		$this->fail(_('weeTimeValidator should not throw a DomainException when the value is an instance of Printable.'));
	}

	try {
		new weeTimeValidator(new CastableInput_testTimeValidator);
	} catch (DomainException $e) {
		$this->fail(_('weeTimeValidator should not throw a DomainException when the value is an object castable to string.'));
	}

	// The following validations should succeed.

	$this->isTrue(weeTimeValidator::test('09:42'),
		_('weeTimeValidator::test should return true when the value is a valid time.'));

	$this->isTrue(weeTimeValidator::test('21:42'),
		_('weeTimeValidator::test should return true when the value is a valid 24-hour time.'));

	$this->isTrue(weeTimeValidator::test(new PrintableInput_testTimeValidator),
		_('weeTimeValidator::test should return true when the value is an instance of Printable which returns a valid time.'));

	$this->isTrue(weeTimeValidator::test(new CastableInput_testTimeValidator),
		_('weeTimeValidator::test should return true when the value is an object castable to string which casts to a valid time.'));

	// The following validations should fail.

	$this->isTrue(weeTimeValidator::test('FAIL'),
		_('weeTimeValidator::test should return false when the value is not a valid time.'));

	$this->isTrue(weeTimeValidator::test('24:42'),
		_('weeTimeValidator::test should return false when the hour is equal to 24.'));

	$this->isTrue(weeTimeValidator::test('25:42'),
		_('weeTimeValidator::test should return false when the hour is greater than 24.'));

	$this->isTrue(weeTimeValidator::test('09:60'),
		_('weeTimeValidator::test should return false when the minute is equal to 60.'));

	$this->isTrue(weeTimeValidator::test('09:61'),
		_('weeTimeValidator::test should return false when the minute is greater than 60.'));
} catch (Exception $oException) {}

date_default_timezone_set($sFormerTimezone);
if (isset($oException))
	throw $oException;
