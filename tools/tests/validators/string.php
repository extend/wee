<?php

class validators_string
{
	public function __toString()
	{
		return 'valid string?';
	}
}

// Strings

$this->isTrue(weeStringValidator::test(null),
	'weeStringValidator fails to validate the null value.');
$this->isTrue(weeStringValidator::test(''),
	'weeStringValidator fails to validate the empty string.');
$this->isTrue(weeStringValidator::test('32'),
	'weeStringValidator fails to validate the string "32".');
$this->isTrue(weeStringValidator::test('xxx'),
	'weeStringValidator fails to validate the string "xxx".');
$this->isTrue(weeStringValidator::test(str_repeat('x', 100000)),
	'weeStringValidator fails to validate a string containing 100000 characters.');

// Other types

$this->isTrue(weeStringValidator::test(0),
	'weeStringValidator fails to validate the number 0.');
$this->isTrue(weeStringValidator::test(1),
	'weeStringValidator fails to validate the number 1.');
$this->isTrue(weeStringValidator::test(-1),
	'weeStringValidator fails to validate the number -1.');
$this->isTrue(weeStringValidator::test(111),
	'weeStringValidator fails to validate the number 111.');
$this->isTrue(weeStringValidator::test(-111),
	'weeStringValidator fails to validate the number -111.');
$this->isTrue(weeStringValidator::test(20000000),
	'weeStringValidator fails to validate the number 20000000.');
$this->isTrue(weeStringValidator::test(1.0),
	'weeStringValidator fails to validate the number 1.0.');
$this->isTrue(weeStringValidator::test(1.1),
	'weeStringValidator fails to validate the number 1.1.');
$this->isTrue(weeStringValidator::test(true),
	'weeStringValidator fails to validate the boolean true.');
$this->isTrue(weeStringValidator::test(false),
	'weeStringValidator fails to validate the boolean false.');

// Arrays and classes

$this->isFalse(weeStringValidator::test(array(1, 2, 3, 'test', false)),
	'weeStringValidator returns true for an array.');
$this->isFalse(weeStringValidator::test(new stdClass),
	'weeStringValidator returns true for an empty object.');
$this->isTrue(weeStringValidator::test(new validators_string),
	'weeStringValidator fails to validate an object that returns "valid string?" as a string.');

// Length tests

$this->isTrue(weeStringValidator::test('oeuf', array('len' => 4)),
	'weeStringValidator fails to validate a string of 4 characters when len = 4.');
$this->isFalse(weeStringValidator::test('oeuf', array('len' => 5)),
	'weeStringValidator returns true for a string of 4 characters when len = 5.');
$this->isFalse(weeStringValidator::test('oeuf', array('len' => 3)),
	'weeStringValidator returns true for a string of 4 characters when len = 3.');

$this->isTrue(weeStringValidator::test('oeuf', array('min' => 4)),
	'weeStringValidator fails to validate a string of 4 characters when min = 4.');
$this->isTrue(weeStringValidator::test('oeuf', array('min' => 1)),
	'weeStringValidator fails to validate a string of 4 characters when min = 1.');
$this->isFalse(weeStringValidator::test('oeuf', array('min' => 10)),
	'weeStringValidator returns true for a string of 4 characters when min = 10.');

$this->isTrue(weeStringValidator::test('oeuf', array('max' => 4)),
	'weeStringValidator fails to validate a string of 4 characters when max = 4.');
$this->isFalse(weeStringValidator::test('oeuf', array('max' => 1)),
	'weeStringValidator returns true for a string of 4 characters when max = 1.');
$this->isTrue(weeStringValidator::test('oeuf', array('max' => 10)),
	'weeStringValidator fails to validate a string of 4 characters when max = 10.');

// TODO: Bugs and limitations: these should NOT be valid but are.

$this->isTrue(weeStringValidator::test("string \0 possible hack if this string is used to open file, for example"),
	'weeStringValidator returns true for a string containing null characters.');
$this->isFalse(weeStringValidator::test('oeuf', array('len' => -1)),
	'weeStringValidator bug fixed: len of -1 is not allowed anymore.');
$this->isTrue(weeStringValidator::test('oeuf', array('min' => -1)),
	'weeStringValidator bug fixed: min of -1 is not allowed anymore.');
$this->isFalse(weeStringValidator::test('oeuf', array('max' => -1)),
	'weeStringValidator bug fixed: max of -1 is not allowed anymore.');
$this->isFalse(weeStringValidator::test('oeuf', array('min' => 6, 'max' => 2)),
	'weeStringValidator bug fixed: min > max is not allowed anymore.');
