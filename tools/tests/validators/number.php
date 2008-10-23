<?php

class PrintableInput_testNumberValidator implements Printable {
	public function toString() {
		return '42';
	}
}

class CastableInput_testNumberValidator {
	public function __toString() {
		return '42';
	}
}

// weeNumberValidator should throw a DomainException when the value to validate is neither an integer,
// a float or a string, an instance of Printable or an object castable to string.

try {
	new weeNumberValidator(new stdClass);
	$this->fail(_('weeNumberValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
} catch (DomainException $e) {}

try {
	new weeNumberValidator(true);
	$this->fail(_('weeNumberValidator should throw a DomainException when the value is a boolean.'));
} catch (DomainException $e) {}

try {
	new weeNumberValidator(null);
	$this->fail(_('weeNumberValidator should throw a DomainException when the value is null.'));
} catch (DomainException $e) {}

try {
	new weeNumberValidator(array());
	$this->fail(_('weeNumberValidator should throw a DomainException when the value is an array.'));
} catch (DomainException $e) {}

try {
	new weeNumberValidator(42);
} catch (DomainException $e) {
	$this->fail(_('weeNumberValidator should not throw a DomainException when the value is an integer.'));
}

try {
	new weeNumberValidator(42.42);
} catch (DomainException $e) {
	$this->fail(_('weeNumberValidator should not throw a DomainException when the value is a float.'));
}

try {
	new weeNumberValidator('42');
} catch (DomainException $e) {
	$this->fail(_('weeNumberValidator should not throw a DomainException when the value is a string.'));
}

try {
	new weeNumberValidator(new PrintableInput_testNumberValidator);
} catch (DomainException $e) {
	$this->fail(_('weeNumberValidator should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	new weeNumberValidator(new CastableInput_testNumberValidator);
} catch (DomainException $e) {
	$this->fail(_('weeNumberValidator should not throw a DomainException when the value is an object castable to string.'));
}

// weeNumberValidator should throw a DomainException when the `min` argument is not a valid number.

try {
	new weeNumberValidator(42, array('min' => 'not_a_number'));
	$this->fail(_('weeNumberValidator should throw a DomainException when the `min` argument is not a valid number.'));
} catch (DomainException $e) {}

try {
	new weeNumberValidator(42, array('min' => 42));
} catch (DomainException $e) {
	$this->fail(_('weeNumberValidator should not throw a DomainException when the `min` argument is a valid number.'));
}

// weeNumberValidator should throw a DomainException when the `max` argument is not a valid number.

try {
	new weeNumberValidator(42, array('max' => 'not_a_number'));
	$this->fail(_('weeNumberValidator should throw a DomainException when the `max` argument is not a number.'));
} catch (DomainException $e) {}

try {
	new weeNumberValidator(42, array('max' => 42));
} catch (DomainException $e) {
	$this->fail(_('weeNumberValidator should not throw a DomainException when the `max` argument is a valid number.'));
}

// weeNumberValidator should throw an InvalidArgumentException when the `min` and `max` arguments do not form a valid number range.

try {
	new weeNumberValidator(42, array('min' => 43, 'max' => 41));
	$this->fail(_('weeNumberValidator should throw an InvalidArgumentException when the `min` argument is greater than the `max` one.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeNumberValidator(42, array('min' => 42, 'max' => 42));
	$this->fail(_('weeNumberValidator should throw an InvalidArgumentException when the `min` and `max` arguments are equal.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeNumberValidator(42, array('min' => 41, 'max' => 43));
} catch (InvalidArgumentException $e) {
	$this->fail(_('weeNumberValidator should not throw an InvalidArgumentException when the `min` argument is smaller than the `max` one.'));
}

// Integer (int)

$this->isTrue(weeNumberValidator::test(0),
	'weeNumberValidator fails to validate the number 0.');
$this->isTrue(weeNumberValidator::test(1),
	'weeNumberValidator fails to validate the number 1.');
$this->isTrue(weeNumberValidator::test(-1),
	'weeNumberValidator fails to validate the number -1.');
$this->isTrue(weeNumberValidator::test(111),
	'weeNumberValidator fails to validate the number 111.');
$this->isTrue(weeNumberValidator::test(-111),
	'weeNumberValidator fails to validate the number -111.');
$this->isTrue(weeNumberValidator::test(1.0),
	'weeNumberValidator fails to validate the number 1.0.');
$this->isFalse(weeNumberValidator::test(1.1),
	'weeNumberValidator returns true for the float 1.1 when it should validate integers only.');
$this->isTrue(weeNumberValidator::test(20000000),
	'weeNumberValidator fails to validate the number 20000000.');

// Integer (string)

$this->isTrue(weeNumberValidator::test('656'),
	'weeNumberValidator fails to validate the string "656".');
$this->isFalse(weeNumberValidator::test('1.0'),
	'weeNumberValidator returns true for the string "1.0" when it should validate integers only.');
$this->isFalse(weeNumberValidator::test('1.1'),
	'weeNumberValidator returns true for the string "1.1" when it should validate integers only.');

// Float (float)

$this->isTrue(weeNumberValidator::test(0, array('format' => 'float')),
	'weeNumberValidator fails to validate the number 0 when it should validate both integer and float.');
$this->isTrue(weeNumberValidator::test(1, array('format' => 'float')),
	'weeNumberValidator fails to validate the number 1 when it should validate both integer and float.');
$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float')),
	'weeNumberValidator fails to validate the number 1.1 when it should validate both integer and float.');

// Float (string)

$this->isTrue(weeNumberValidator::test('0', array('format' => 'float')),
	'weeNumberValidator fails to validate the string "0" when it should validate both integer and float.');
$this->isTrue(weeNumberValidator::test('0.0', array('format' => 'float')),
	'weeNumberValidator fails to validate the string "0.0" when it should validate both integer and float.');
$this->isTrue(weeNumberValidator::test('1', array('format' => 'float')),
	'weeNumberValidator fails to validate the string "1" when it should validate both integer and float.');
$this->isTrue(weeNumberValidator::test('1.1', array('format' => 'float')),
	'weeNumberValidator fails to validate the string "1.1" when it should validate both integer and float.');
$this->isFalse(weeNumberValidator::test('1.1.1', array('format' => 'float')),
	'weeNumberValidator returns true for the string "1.1.1".');

// Bad values

$this->isFalse(weeNumberValidator::test(''),
	'weeNumberValidator returns true for the empty string.');
$this->isFalse(weeNumberValidator::test('32f'),
	'weeNumberValidator returns true for the string "32f".');
$this->isFalse(weeNumberValidator::test('xxx'),
	'weeNumberValidator returns true for the string "xxx".');

// Integer min/max

$this->isTrue(weeNumberValidator::test(0, array('min' => -10)),
	'weeNumberValidator fails to validate 0 >= -10.');
$this->isFalse(weeNumberValidator::test(0, array('min' => 10)),
	'weeNumberValidator returns true for 0 >= 10.');
$this->isTrue(weeNumberValidator::test(0, array('max' => 10)),
	'weeNumberValidator fails to validate 0 <= 10.');
$this->isFalse(weeNumberValidator::test(0, array('max' => -10)),
	'weeNumberValidator returns true for 0 <= -10.');

// Float min/max

$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 0)),
	'weeNumberValidator fails to validate 1.1 >= 0.');
$this->isFalse(weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 2)),
	'weeNumberValidator returns true for 1.1 >= 2.');
$this->isFalse(weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 0)),
	'weeNumberValidator returns true for 1.1 <= 0.');
$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 2)),
	'weeNumberValidator fails to validate 1.1 <= 2.');
$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 1.0)),
	'weeNumberValidator fails to validate 1.1 >= 1.0.');
$this->isFalse(weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 1.2)),
	'weeNumberValidator returns true for 1.1 >= 1.2.');
$this->isFalse(weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 1.0)),
	'weeNumberValidator returns true for 1.1 <= 1.0.');
$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 1.2)),
	'weeNumberValidator fails to validate 1.1 <= 1.2.');

// Objects

$this->isTrue(weeNumberValidator::test(new PrintableInput_testNumberValidator),
	_('weeNumberValidator::test should return true when the value is an instance of Printable which returns a valid number.'));

$this->isTrue(weeNumberValidator::test(new CastableInput_testNumberValidator),
	_('weeNumberValidator::test should return true when the value is an object castable to string which casts to a valid number.'));
