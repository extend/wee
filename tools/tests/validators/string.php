<?php

class PrintableInput_testStringValidator implements Printable {
	public function toString() {
		return '42';
	}
}

class CastableInput_testStringValidator {
	public function __toString() {
		return '42';
	}
}

// weeStringValidator should throw a DomainException if the value is neither a scalar, the null value, an array,
// an instance of Printable or an object castable to string.

$o = new weeStringValidator;

try {
	$o->setValue(new stdClass);
	$this->fail(_WT('weeStringValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
} catch (DomainException $e) {}

try {
	$o->setValue(true);
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the value is a boolean.'));
}

try {
	$o->setValue(null);
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the value is null.'));
}

try {
	$o->setValue(array());
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the value is an array.'));
}

try {
	$o->setValue(42);
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the value is an integer.'));
}

try {
	$o->setValue(42.42);
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the value is a float.'));
}

try {
	$o->setValue('win');
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the value is a string.'));
}

try {
	$o->setValue(new PrintableInput_testStringValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	$o->setValue(new CastableInput_testStringValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the value is an object castable to string.'));
}

// weeStringValidator should throw a DomainException when the `len` argument is not a valid length.

try {
	new weeStringValidator(array('len' => 'not_a_length'));
	$this->fail(_WT('weeStringValidator should throw a DomainException when the `len` argument is not a valid length.'));
} catch (DomainException $e) {}

try {
	new weeStringValidator(array('len' => -1));
	$this->fail(_WT('weeStringValidator should throw a DomainException when the `len` argument is negative.'));
} catch (DomainException $e) {}

try {
	new weeStringValidator(array('len' => 3));
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the `len` argument is a valid length.'));
}

try {
	new weeStringValidator(array('len' => 0));
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the `len` argument is equal to 0.'));
}

// weeStringValidator should throw a DomainException when the `min` argument is not a valid length.

try {
	new weeStringValidator(array('min' => 'not_a_length'));
	$this->fail(_WT('weeStringValidator should throw a DomainException when the `min` argument is not a valid length.'));
} catch (DomainException $e) {}

try {
	new weeStringValidator(array('min' => -1));
	$this->fail(_WT('weeStringValidator should throw a DomainException when the `min` argument is negative.'));
} catch (DomainException $e) {}

try {
	new weeStringValidator(array('min' => 2));
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the `min` argument is a valid length.'));
}

try {
	new weeStringValidator(array('min' => 0));
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the `min` argument is equal to 0.'));
}

// weeStringValidator should throw a DomainException when the `max` argument is not a valid length.

try {
	new weeStringValidator(array('max' => 'not_a_length'));
	$this->fail(_WT('weeStringValidator should throw a DomainException when the `max` argument is not a valid length.'));
} catch (DomainException $e) {}

try {
	new weeStringValidator(array('max' => -1));
	$this->fail(_WT('weeStringValidator should throw a DomainException when the `max` argument is negative.'));
} catch (DomainException $e) {}

try {
	new weeStringValidator(array('max' => 4));
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the `max` argument is a valid length.'));
}

try {
	new weeStringValidator(array('max' => 0));
} catch (DomainException $e) {
	$this->fail(_WT('weeStringValidator should not throw a DomainException when the `max` argument is equal to 0.'));
}

// weeStringValidator should throw an InvalidArgumentException when the `min` and `max` arguments do not form a valid length range.

try {
	new weeStringValidator(array('min' => 4, 'max' => 2));
	$this->fail(_WT('weeStringValidator should throw an InvalidArgumentException when the `min` argument is greater than the `max` one.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeStringValidator(array('min' => 3, 'max' => 3));
	$this->fail(_WT('weeStringValidator should throw an InvalidArgumentException when the `min` and `max` arguments are equal.'));
} catch (InvalidArgumentException $e) {}

try {
	new weeStringValidator(array('min' => 2, 'max' => 4));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeStringValidator should not throw an InvalidArgumentException when the `min` argument is smaller than the `max` one.'));
}

// weeStringValidator should throw an InvalidArgumentException when the `len` and one of the `min` and `max` arguments are both specified.

try {
	new weeStringValidator(array('len' => 3, 'min' => 2, 'max' => 4));
	$this->fail(_WT('weeStringValidator should throw an InvalidArgumentException when the `len` and one of the `min` and `max` arguments are both specified.'));
} catch (InvalidArgumentException $e) {}

// Strings

$this->isTrue(weeStringValidator::test(null),
	_WT('weeStringValidator fails to validate the null value.'));
$this->isTrue(weeStringValidator::test(''),
	_WT('weeStringValidator fails to validate the empty string.'));
$this->isTrue(weeStringValidator::test('32'),
	_WT('weeStringValidator fails to validate the string "32".'));
$this->isTrue(weeStringValidator::test('xxx'),
	_WT('weeStringValidator fails to validate the string "xxx".'));
$this->isTrue(weeStringValidator::test(str_repeat('x', 100000)),
	_WT('weeStringValidator fails to validate a string containing 100000 characters.'));

// Integers

$this->isTrue(weeStringValidator::test(0),
	_WT('weeStringValidator fails to validate the number 0.'));
$this->isTrue(weeStringValidator::test(1),
	_WT('weeStringValidator fails to validate the number 1.'));
$this->isTrue(weeStringValidator::test(-1),
	_WT('weeStringValidator fails to validate the number -1.'));
$this->isTrue(weeStringValidator::test(111),
	_WT('weeStringValidator fails to validate the number 111.'));
$this->isTrue(weeStringValidator::test(-111),
	_WT('weeStringValidator fails to validate the number -111.'));
$this->isTrue(weeStringValidator::test(20000000),
	_WT('weeStringValidator fails to validate the number 20000000.'));

// Floats

$this->isTrue(weeStringValidator::test(1.0),
	_WT('weeStringValidator fails to validate the number 1.0.'));
$this->isTrue(weeStringValidator::test(1.1),
	_WT('weeStringValidator fails to validate the number 1.1.'));

// Booleans

$this->isTrue(weeStringValidator::test(true),
	_WT('weeStringValidator fails to validate the boolean true.'));
$this->isTrue(weeStringValidator::test(false),
	_WT('weeStringValidator fails to validate the boolean false.'));

// Arrays

$this->isFalse(weeStringValidator::test(array(1, 2, 3, 'test', false)),
	_WT('weeStringValidator returns true for an array.'));

// Length tests

$this->isTrue(weeStringValidator::test('oeuf', array('len' => 4)),
	_WT('weeStringValidator fails to validate a string of 4 characters when len = 4.'));
$this->isFalse(weeStringValidator::test('oeuf', array('len' => 5)),
	_WT('weeStringValidator returns true for a string of 4 characters when len = 5.'));
$this->isFalse(weeStringValidator::test('oeuf', array('len' => 3)),
	_WT('weeStringValidator returns true for a string of 4 characters when len = 3.'));

$this->isTrue(weeStringValidator::test('oeuf', array('min' => 4)),
	_WT('weeStringValidator fails to validate a string of 4 characters when min = 4.'));
$this->isTrue(weeStringValidator::test('oeuf', array('min' => 1)),
	_WT('weeStringValidator fails to validate a string of 4 characters when min = 1.'));
$this->isFalse(weeStringValidator::test('oeuf', array('min' => 10)),
	_WT('weeStringValidator returns true for a string of 4 characters when min = 10.'));

$this->isTrue(weeStringValidator::test('oeuf', array('max' => 4)),
	_WT('weeStringValidator fails to validate a string of 4 characters when max = 4.'));
$this->isFalse(weeStringValidator::test('oeuf', array('max' => 1)),
	_WT('weeStringValidator returns true for a string of 4 characters when max = 1.'));
$this->isTrue(weeStringValidator::test('oeuf', array('max' => 10)),
	_WT('weeStringValidator fails to validate a string of 4 characters when max = 10.'));

// NUL character

$this->isFalse(weeStringValidator::test("string \0 possible hack if this string is used to open file, for example"),
	_WT('weeStringValidator returns true for a string containing null characters.'));

// Objects

$this->isTrue(weeStringValidator::test(new PrintableInput_testStringValidator),
	_WT('weeStringValidator::test should return true when the value is an instance of Printable which returns a valid string.'));

$this->isTrue(weeStringValidator::test(new CastableInput_testStringValidator),
	_WT('weeStringValidator::test should return true when the value is an object castable to string which casts to a valid string.'));
