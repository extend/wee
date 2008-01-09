<?php

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
$this->isTrue(weeNumberValidator::test('20000000000000000'),
	'weeNumberValidator fails to validate the string "20000000000000000".');
$this->isTrue(weeNumberValidator::test('-20000000000000000'),
	'weeNumberValidator fails to validate the string "-20000000000000000".');
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

$this->isFalse(weeNumberValidator::test(null),
	'weeNumberValidator returns true for a null value.');
$this->isFalse(weeNumberValidator::test(''),
	'weeNumberValidator returns true for the empty string.');
$this->isFalse(weeNumberValidator::test('32f'),
	'weeNumberValidator returns true for the string "32f".');
$this->isFalse(weeNumberValidator::test('xxx'),
	'weeNumberValidator returns true for the string "xxx".');
$this->isFalse(weeNumberValidator::test(true),
	'weeNumberValidator returns true for the boolean true.');
$this->isFalse(weeNumberValidator::test(new stdClass),
	'weeNumberValidator returns true for an empty object.');

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
