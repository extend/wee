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

$o = new weeNumberValidator;

$this->isEqual(array('format' => 'int'), $o->getArgs(),
	_WT('weeNumberValidator does not return the expected default `format` argument.'));

// weeNumberValidator should throw a DomainException when the value to validate is neither an integer,
// a float or a string, an instance of Printable or an object castable to string.

try {
	$o->setValue(new stdClass);
	$this->fail(_WT('weeNumberValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
} catch (DomainException $e) {}

try {
	$o->setValue(true);
	$this->fail(_WT('weeNumberValidator should throw a DomainException when the value is a boolean.'));
} catch (DomainException $e) {}

try {
	$o->setValue(null);
	$this->fail(_WT('weeNumberValidator should throw a DomainException when the value is null.'));
} catch (DomainException $e) {}

try {
	$o->setValue(array());
	$this->fail(_WT('weeNumberValidator should throw a DomainException when the value is an array.'));
} catch (DomainException $e) {}

try {
	$o->setValue(42);
} catch (DomainException $e) {
	$this->fail(_WT('weeNumberValidator should not throw a DomainException when the value is an integer.'));
}

try {
	$o->setValue(42.42);
} catch (DomainException $e) {
	$this->fail(_WT('weeNumberValidator should not throw a DomainException when the value is a float.'));
}

try {
	$o->setValue('42');
} catch (DomainException $e) {
	$this->fail(_WT('weeNumberValidator should not throw a DomainException when the value is a string.'));
}

try {
	$o->setValue(new PrintableInput_testNumberValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeNumberValidator should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	$o->setValue(new CastableInput_testNumberValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeNumberValidator should not throw a DomainException when the value is an object castable to string.'));
}

// weeNumberValidator should throw an InvalidArgumentException when the `format` argument is invalid.

try {
	new weeNumberValidator(array('format' => 'fail'));
	$this->fail(_WT('weeNumberValidator should throw an InvalidArgumentException when the `format` argument is invalid.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeNumberValidator(array('format' => 'int'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeNumberValidator should not throw an InvalidArgumentException when the `format` argument is "int".'));
}

try {
	new weeNumberValidator(array('format' => 'float'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeNumberValidator should not throw an InvalidArgumentException when the `format` argument is "float".'));
}

// weeNumberValidator should throw a DomainException when the `min` argument is not a valid number.

try {
	new weeNumberValidator(array('min' => 'not_a_number'));
	$this->fail(_WT('weeNumberValidator should throw a DomainException when the `min` argument is not a valid number.'));
} catch (DomainException $e) {}

try {
	new weeNumberValidator(array('min' => 42));
} catch (DomainException $e) {
	$this->fail(_WT('weeNumberValidator should not throw a DomainException when the `min` argument is a valid number.'));
}

// weeNumberValidator should throw a DomainException when the `max` argument is not a valid number.

try {
	new weeNumberValidator(array('max' => 'not_a_number'));
	$this->fail(_WT('weeNumberValidator should throw a DomainException when the `max` argument is not a number.'));
} catch (DomainException $e) {}

try {
	new weeNumberValidator(array('max' => 42));
} catch (DomainException $e) {
	$this->fail(_WT('weeNumberValidator should not throw a DomainException when the `max` argument is a valid number.'));
}

// weeNumberValidator should throw an InvalidArgumentException when the `min` and `max` arguments do not form a valid number range.

try {
	new weeNumberValidator(array('min' => 43, 'max' => 41));
	$this->fail(_WT('weeNumberValidator should throw an InvalidArgumentException when the `min` argument is greater than the `max` one.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeNumberValidator(array('min' => 42, 'max' => 42));
	$this->fail(_WT('weeNumberValidator should throw an InvalidArgumentException when the `min` and `max` arguments are equal.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeNumberValidator(array('min' => 41, 'max' => 43));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeNumberValidator should not throw an InvalidArgumentException when the `min` argument is smaller than the `max` one.'));
}

// Integer (int)

$this->isTrue(weeNumberValidator::test(0),
	_WT('weeNumberValidator fails to validate the number 0.'));
$this->isTrue(weeNumberValidator::test(1),
	_WT('weeNumberValidator fails to validate the number 1.'));
$this->isTrue(weeNumberValidator::test(-1),
	_WT('weeNumberValidator fails to validate the number -1.'));
$this->isTrue(weeNumberValidator::test(111),
	_WT('weeNumberValidator fails to validate the number 111.'));
$this->isTrue(weeNumberValidator::test(-111),
	_WT('weeNumberValidator fails to validate the number -111.'));
$this->isTrue(weeNumberValidator::test(1.0),
	_WT('weeNumberValidator fails to validate the number 1.0.'));
$this->isFalse(weeNumberValidator::test(1.1),
	_WT('weeNumberValidator returns true for the float 1.1 when it should validate integers only.'));
$this->isTrue(weeNumberValidator::test(20000000),
	_WT('weeNumberValidator fails to validate the number 20000000.'));

// Integer (string)

$this->isTrue(weeNumberValidator::test('656'),
	_WT('weeNumberValidator fails to validate the string "656".'));
$this->isFalse(weeNumberValidator::test('1.0'),
	_WT('weeNumberValidator returns true for the string "1.0" when it should validate integers only.'));
$this->isFalse(weeNumberValidator::test('1.1'),
	_WT('weeNumberValidator returns true for the string "1.1" when it should validate integers only.'));

// Float (float)

$this->isTrue(weeNumberValidator::test(0, array('format' => 'float')),
	_WT('weeNumberValidator fails to validate the number 0 when it should validate both integer and float.'));
$this->isTrue(weeNumberValidator::test(1, array('format' => 'float')),
	_WT('weeNumberValidator fails to validate the number 1 when it should validate both integer and float.'));
$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float')),
	_WT('weeNumberValidator fails to validate the number 1.1 when it should validate both integer and float.'));

// Float (string)

$this->isTrue(weeNumberValidator::test('0', array('format' => 'float')),
	_WT('weeNumberValidator fails to validate the string "0" when it should validate both integer and float.'));
$this->isTrue(weeNumberValidator::test('0.0', array('format' => 'float')),
	_WT('weeNumberValidator fails to validate the string "0.0" when it should validate both integer and float.'));
$this->isTrue(weeNumberValidator::test('1', array('format' => 'float')),
	_WT('weeNumberValidator fails to validate the string "1" when it should validate both integer and float.'));
$this->isTrue(weeNumberValidator::test('1.1', array('format' => 'float')),
	_WT('weeNumberValidator fails to validate the string "1.1" when it should validate both integer and float.'));
$this->isFalse(weeNumberValidator::test('1.1.1', array('format' => 'float')),
	_WT('weeNumberValidator returns true for the string "1.1.1".'));

// Bad values

$this->isFalse(weeNumberValidator::test(''),
	_WT('weeNumberValidator returns true for the empty string.'));
$this->isFalse(weeNumberValidator::test('32f'),
	_WT('weeNumberValidator returns true for the string "32f".'));
$this->isFalse(weeNumberValidator::test('xxx'),
	_WT('weeNumberValidator returns true for the string "xxx".'));

// Integer min/max

$this->isTrue(weeNumberValidator::test(0, array('min' => -10)),
	_WT('weeNumberValidator fails to validate 0 >= -10.'));
$this->isFalse(weeNumberValidator::test(0, array('min' => 10)),
	_WT('weeNumberValidator returns true for 0 >= 10.'));
$this->isTrue(weeNumberValidator::test(0, array('max' => 10)),
	_WT('weeNumberValidator fails to validate 0 <= 10.'));
$this->isFalse(weeNumberValidator::test(0, array('max' => -10)),
	_WT('weeNumberValidator returns true for 0 <= -10.'));

// Float min/max

$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 0)),
	_WT('weeNumberValidator fails to validate 1.1 >= 0.'));
$this->isFalse(weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 2)),
	_WT('weeNumberValidator returns true for 1.1 >= 2.'));
$this->isFalse(weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 0)),
	_WT('weeNumberValidator returns true for 1.1 <= 0.'));
$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 2)),
	_WT('weeNumberValidator fails to validate 1.1 <= 2.'));
$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 1.0)),
	_WT('weeNumberValidator fails to validate 1.1 >= 1.0.'));
$this->isFalse(weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 1.2)),
	_WT('weeNumberValidator returns true for 1.1 >= 1.2.'));
$this->isFalse(weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 1.0)),
	_WT('weeNumberValidator returns true for 1.1 <= 1.0.'));
$this->isTrue(weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 1.2)),
	_WT('weeNumberValidator fails to validate 1.1 <= 1.2.'));

// The value is outside the range.

$this->isFalse(weeNumberValidator::test(42, array('min' => 43, 'max' => 44)),
	_WT('weeNumberValidator should return false if the value is under the range of the `min` and `max` arguments.'));

$this->isFalse(weeNumberValidator::test(42, array('min' => 40, 'max' => 41)),
	_WT('weeNumberValidator should return false if the value is over the range of the `min` and `max` arguments.'));

$this->isTrue(weeNumberValidator::test(42, array('min' => 41, 'max' => 43)),
	_WT('weeNumberValidator should return true if the value is in the range of the `min` and `max` arguments.'));

// Objects

$this->isTrue(weeNumberValidator::test(new PrintableInput_testNumberValidator),
	_WT('weeNumberValidator::test should return true when the value is an instance of Printable which returns a valid number.'));

$this->isTrue(weeNumberValidator::test(new CastableInput_testNumberValidator),
	_WT('weeNumberValidator::test should return true when the value is an object castable to string which casts to a valid number.'));
