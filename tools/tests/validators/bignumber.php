<?php

class PrintableInput_testBigNumberValidator implements Printable {
	public function toString() {
		return '42';
	}
}

class CastableInput_testBigNumberValidator {
	public function __toString() {
		return '42';
	}
}

// weeBigNumberValidator should throw a DomainException when the value to validate is neither an integer,
// a float or a string, an instance of Printable or an object castable to string.

$o = new weeBigNumberValidator;

try {
	$o->setValue(new stdClass);
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
} catch (DomainException $e) {}

try {
	$o->setValue(true);
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the value is a boolean.'));
} catch (DomainException $e) {}

try {
	$o->setValue(null);
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the value is null.'));
} catch (DomainException $e) {}

try {
	$o->setValue(array());
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the value is an array.'));
} catch (DomainException $e) {}

try {
	$o->setValue(42);
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the value is an integer.'));
} catch (DomainException $e) {
}

try {
	$o->setValue(42.42);
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the value is a float.'));
} catch (DomainException $e) {
}

try {
	$o->setValue('42');
} catch (DomainException $e) {
	$this->fail(_WT('weeBigNumberValidator should not throw a DomainException when the value is a string.'));
}

try {
	$o->setValue(new PrintableInput_testBigNumberValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeBigNumberValidator should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	$o->setValue(new CastableInput_testBigNumberValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeBigNumberValidator should not throw a DomainException when the value is an object castable to string.'));
}

// weeBigNumberValidator should throw a DomainException when the `min` argument is not a valid number.

try {
	new weeBigNumberValidator(array('min' => 'not_a_number'));
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the `min` argument is not a valid number.'));
} catch (DomainException $e) {}

try {
	new weeBigNumberValidator(array('min' => 42));
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the `min` argument is not given as a string.'));
} catch (DomainException $e) {}

try {
	new weeBigNumberValidator(array('min' => '42'));
} catch (DomainException $e) {
	$this->fail(_WT('weeBigNumberValidator should not throw a DomainException when the `min` argument is a valid number.'));
}

// weeBigNumberValidator should throw a DomainException when the `max` argument is not a valid number.

try {
	new weeBigNumberValidator(array('max' => 'not_a_number'));
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the `max` argument is not a number.'));
} catch (DomainException $e) {}

try {
	new weeBigNumberValidator(array('max' => 42));
	$this->fail(_WT('weeBigNumberValidator should throw a DomainException when the `max` argument is not given as a string.'));
} catch (DomainException $e) {}

try {
	new weeBigNumberValidator(array('max' => '42'));
} catch (DomainException $e) {
	$this->fail(_WT('weeBigNumberValidator should not throw a DomainException when the `max` argument is a valid number.'));
}

// weeBigNumberValidator should throw an InvalidArgumentException when the `min` and `max` arguments do not form a valid number range.

try {
	new weeBigNumberValidator(array('min' => '43', 'max' => '41'));
	$this->fail(_WT('weeBigNumberValidator should throw an InvalidArgumentException when the `min` argument is greater than the `max` one.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeBigNumberValidator(array('min' => '42', 'max' => '42'));
	$this->fail(_WT('weeBigNumberValidator should throw an InvalidArgumentException when the `min` and `max` arguments are equal.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeBigNumberValidator(array('min' => '41', 'max' => '43'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeBigNumberValidator should not throw an InvalidArgumentException when the `min` argument is smaller than the `max` one.'));
}

// Integer (string)

$this->isTrue(weeBigNumberValidator::test('656'),
	'weeBigNumberValidator fails to validate the string "656".');
$this->isFalse(weeBigNumberValidator::test('1.0'),
	'weeBigNumberValidator returns true for the string "1.0" when it should validate integers only.');
$this->isFalse(weeBigNumberValidator::test('1.1'),
	'weeBigNumberValidator returns true for the string "1.1" when it should validate integers only.');

// Float (string)

$this->isTrue(weeBigNumberValidator::test('0', array('format' => 'float')),
	'weeBigNumberValidator fails to validate the string "0" when it should validate both integer and float.');
$this->isTrue(weeBigNumberValidator::test('0.0', array('format' => 'float')),
	'weeBigNumberValidator fails to validate the string "0.0" when it should validate both integer and float.');
$this->isTrue(weeBigNumberValidator::test('1', array('format' => 'float')),
	'weeBigNumberValidator fails to validate the string "1" when it should validate both integer and float.');
$this->isTrue(weeBigNumberValidator::test('1.1', array('format' => 'float')),
	'weeBigNumberValidator fails to validate the string "1.1" when it should validate both integer and float.');
$this->isFalse(weeBigNumberValidator::test('1.1.1', array('format' => 'float')),
	'weeBigNumberValidator returns true for the string "1.1.1".');

// Bad values

$this->isFalse(weeBigNumberValidator::test(''),
	'weeBigNumberValidator returns true for the empty string.');
$this->isFalse(weeBigNumberValidator::test('32f'),
	'weeBigNumberValidator returns true for the string "32f".');
$this->isFalse(weeBigNumberValidator::test('xxx'),
	'weeBigNumberValidator returns true for the string "xxx".');

// Integer min/max

$this->isTrue(weeBigNumberValidator::test('0', array('min' => '-10')),
	'weeBigNumberValidator fails to validate 0 >= -10.');
$this->isFalse(weeBigNumberValidator::test('0', array('min' => '10')),
	'weeBigNumberValidator returns true for 0 >= 10.');
$this->isTrue(weeBigNumberValidator::test('0', array('max' => '10')),
	'weeBigNumberValidator fails to validate 0 <= 10.');
$this->isFalse(weeBigNumberValidator::test('0', array('max' => '-10')),
	'weeBigNumberValidator returns true for 0 <= -10.');

// Float min/max

$this->isTrue(weeBigNumberValidator::test('1.1', array('format' => 'float', 'min' => '0')),
	'weeBigNumberValidator fails to validate 1.1 >= 0.');
$this->isFalse(weeBigNumberValidator::test('1.1', array('format' => 'float', 'min' => '2')),
	'weeBigNumberValidator returns true for 1.1 >= 2.');
$this->isFalse(weeBigNumberValidator::test('1.1', array('format' => 'float', 'max' => '0')),
	'weeBigNumberValidator returns true for 1.1 <= 0.');
$this->isTrue(weeBigNumberValidator::test('1.1', array('format' => 'float', 'max' => '2')),
	'weeBigNumberValidator fails to validate 1.1 <= 2.');
$this->isTrue(weeBigNumberValidator::test('1.1', array('format' => 'float', 'min' => '1.0')),
	'weeBigNumberValidator fails to validate 1.1 >= 1.0.');
$this->isFalse(weeBigNumberValidator::test('1.1', array('format' => 'float', 'min' => '1.2')),
	'weeBigNumberValidator returns true for 1.1 >= 1.2.');
$this->isFalse(weeBigNumberValidator::test('1.1', array('format' => 'float', 'max' => '1.0')),
	'weeBigNumberValidator returns true for 1.1 <= 1.0.');
$this->isTrue(weeBigNumberValidator::test('1.1', array('format' => 'float', 'max' => '1.2')),
	'weeBigNumberValidator fails to validate 1.1 <= 1.2.');

// Big Numbers

$this->isTrue(weeBigNumberValidator::test('12345678901234567890'),
	_WT('weeBigNumberValidator::test should return true for a valid big number'));
$this->isTrue(weeBigNumberValidator::test('-12345678901234567890'),
	_WT('weeBigNumberValidator::test should return true for a valid negative big number'));
$this->isTrue(weeBigNumberValidator::test('12345678901234567890.000042', array('format' => 'float')),
	_WT('weeBigNumberValidator::test should return true for a valid float big number'));
$this->isTrue(weeBigNumberValidator::test('12345678901234567890.000042', array('format' => 'float', 'min' => '12345678901234567890')),
	_WT('weeBigNumberValidator::test should return true for a valid float big number greater than the `min` argument.'));
$this->isFalse(weeBigNumberValidator::test('12345678901234567890.000042', array('format' => 'float', 'max' => '12345678901234567890')),
	_WT('weeBigNumberValidator::test should return false for a valid float big number less than the `max` argument.'));
$this->isTrue(weeBigNumberValidator::test('-0.000000000000000000001', array('format' => 'float', 'min' => '-0.000000000000000000002')),
	_WT('weeBigNumberValidator::test should return true for a valid negative float big number greater than the `min` argument.'));

// The value is outside the range.

$this->isFalse(weeBigNumberValidator::test('42', array('min' => '43', 'max' => '44')),
	_WT('weeBigNumberValidator should return false if the value is under the range of the `min` and `max` arguments.'));

$this->isFalse(weeBigNumberValidator::test('42', array('min' => '40', 'max' => '41')),
	_WT('weeBigNumberValidator should return false if the value is over the range of the `min` and `max` arguments.'));

$this->isTrue(weeBigNumberValidator::test('42', array('min' => '41', 'max' => '43')),
	_WT('weeBigNumberValidator should return true if the value is in the range of the `min` and `max` arguments.'));

// Objects

$this->isTrue(weeBigNumberValidator::test(new PrintableInput_testBigNumberValidator),
	_WT('weeBigNumberValidator::test should return true when the value is an instance of Printable which returns a valid number.'));

$this->isTrue(weeBigNumberValidator::test(new CastableInput_testBigNumberValidator),
	_WT('weeBigNumberValidator::test should return true when the value is an object castable to string which casts to a valid number.'));
