<?php

class PrintableInput_testDateValidator implements Printable {
	public function toString() {
		return '1987-10-29';
	}
}

class CastableInput_testDateValidator {
	public function __toString() {
		return '1987-10-29';
	}
}

$sFormerTimezone = @date_default_timezone_get();
date_default_timezone_set('Europe/Paris');

try {
	$sYesterday	= strftime('%Y-%m-%d', strtotime('yesterday'));
	$sTomorrow	= strftime('%Y-%m-%d', strtotime('tomorrow'));

	// weeDateValidator should throw a DomainException when the value to validate is not a string or an instance of Printable or an object castable to string.

	try {
		new weeDateValidator(new stdClass);
		$this->fail(_('weeDateValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator(true);
		$this->fail(_('weeDateValidator should throw a DomainException when the value is a boolean.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator(null);
		$this->fail(_('weeDateValidator should throw a DomainException when the value is null.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator(array());
		$this->fail(_('weeDateValidator should throw a DomainException when the value is an array.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator(42);
		$this->fail(_('weeDateValidator should throw a DomainException when the value is an integer.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator(42.42);
		$this->fail(_('weeDateValidator should throw a DomainException when the value is a float.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator('1987-10-29');
	} catch (DomainException $e) {
		$this->fail(_('weeDateValidator should not throw a DomainException when the value is a string.'));
	}

	try {
		new weeDateValidator(new PrintableInput_testDateValidator);
	} catch (DomainException $e) {
		$this->fail(_('weeDateValidator should not throw a DomainException when the value is an instance of Printable.'));
	}

	try {
		new weeDateValidator(new CastableInput_testDateValidator);
	} catch (DomainException $e) {
		$this->fail(_('weeDateValidator should not throw a DomainException when the value is an object castable to string.'));
	}

	// weeDateValidator should throw a DomainException when the `min` argument is not a valid date.

	try {
		new weeDateValidator('1987-10-29', array('min' => 42));
		$this->fail(_('weeDateValidator should throw a DomainException when the `min` argument is not a string.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator('1987-10-29', array('min' => 'not_a_date'));
		$this->fail(_('weeDateValidator should throw a DomainException when the `min` argument is not a valid date.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator('1987-10-29', array('min' => '1987-10-29'));
	} catch (DomainException $e) {
		$this->fail(_('weeDateValidator should not throw a DomainException when the `min` argument is a valid date.'));
	}

	try {
		new weeDateValidator('1987-10-29', array('min' => 'current'));
	} catch (DomainException $e) {
		$this->fail(_('weeDateValidator should not throw a DomainException when the `min` argument is "current".'));
	}

	// weeDateValidator should throw a DomainException when the `max` argument is not a valid date.

	try {
		new weeDateValidator('1987-10-29', array('max' => 42));
		$this->fail(_('weeDateValidator should throw a DomainException when the `max` argument is not a string.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator('1987-10-29', array('max' => 'not_a_date'));
		$this->fail(_('weeDateValidator should throw a DomainException when the `max` argument is not a valid date.'));
	} catch (DomainException $e) {}

	try {
		new weeDateValidator('1987-10-29', array('max' => '1987-10-29'));
	} catch (DomainException $e) {
		$this->fail(_('weeDateValidator should not throw a DomainException when the `max` argument is a valid date.'));
	}

	try {
		new weeDateValidator('1987-10-29', array('max' => 'current'));
	} catch (DomainException $e) {
		$this->fail(_('weeDateValidator should not throw a DomainException when the `max` argument is "current".'));
	}

	// weeDateValidator should throw an InvalidArgumentException when the `min` and `max` arguments do not form a valid date range.

	try {
		new weeDateValidator('1987-10-29', array('min' => '1987-10-30', 'max' => '1987-10-28'));
		$this->fail(_('weeDateValidator should throw an InvalidArgumentException when the `min` argument is greater than the `max` one.'));
	} catch (InvalidArgumentException $e) {}

	try {
		new weeDateValidator('1987-10-29', array('min' => '1987-10-29', 'max' => '1987-10-29'));
		$this->fail(_('weeDateValidator should throw an InvalidArgumentException when the `min` and `max` arguments are equal.'));
	} catch (InvalidArgumentException $e) {}

	try {
		new weeDateValidator('1987-10-29', array('min' => '1987-10-28', 'max' => '1987-10-30'));
	} catch (InvalidArgumentException $e) {
		$this->fail(_('weeDateValidator should not throw an InvalidArgumentException when the `min` argument is smaller than the `max` one.'));
	}

	try {
		new weeDateValidator('1987-10-29', array('min' => 'current', 'max' => $sTomorrow));
	} catch (InvalidArgumentException $e) {
		$this->fail(_('weeDateValidator should not throw an InvalidArgumentException when the `min` argument is "current" and the `max` one is tomorrow date.'));
	}

	try {
		new weeDateValidator('1987-10-29', array('min' => $sYesterday, 'max' => 'current'));
	} catch (InvalidArgumentException $e) {
		$this->fail(_('weeDateValidator should not throw an InvalidArgumentException when the `max` argument is "current" and the `min` one is yesterday date.'));
	}

	// The following validations should succeed.

	$this->isTrue(weeDateValidator::test('1987-10-29'),
		_('weeDateValidator::test should return true when the value is a valid date.'));

	$this->isTrue(weeDateValidator::test(new PrintableInput_testDateValidator),
		_('weeDateValidator::test should return true when the value is an instance of Printable which returns a valid date.'));

	$this->isTrue(weeDateValidator::test(new CastableInput_testDateValidator),
		_('weeDateValidator::test should return true when the value is an object castable to string which casts to a valid date.'));

	$this->isTrue(weeDateValidator::test('1987-10-29', array('min' => '1987-10-28')),
		_('weeDateValidator::test should return true when the value is a date greater than the `min` argument.'));

	$this->isTrue(weeDateValidator::test('1987-10-29', array('min' => '1987-10-29')),
		_('weeDateValidator::test should return true when the value is a date equal to the `min` argument.'));

	$this->isTrue(weeDateValidator::test('1987-10-29', array('max' => '1987-10-30')),
		_('weeDateValidator::test should return true when the value is a date smaller than the `max` argument.'));

	$this->isTrue(weeDateValidator::test('1987-10-29', array('max' => '1987-10-29')),
		_('weeDateValidator::test should return true when the value is a date equal to the `max` argument.'));

	$this->isTrue(weeDateValidator::test('1987-10-29', array('min' => '1987-10-28', 'max' => '1987-10-30')),
		_('weeDateValidator::test should return true when the value is a date between the `min` and `max` arguments.'));

	// The following validations should fail.

	$this->isFalse(weeDateValidator::test('FAIL'),
		_('weeDateValidator::test should return false when the value is not a valid date.'));

	$this->isFalse(weeDateValidator::test('1987-10-29', array('min' => '1987-10-30')),
		_('weeDateValidator::test should return false when the value is a date smaller than the `min` argument.'));

	$this->isFalse(weeDateValidator::test('1987-10-29', array('max' => '1987-10-28')),
		_('weeDateValidator::test should return false when the value is a date greater than the `max` argument.'));
} catch (Exception $oException) {}

date_default_timezone_set($sFormerTimezone);
if (isset($oException))
	throw $oException;
