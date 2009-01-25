<?php

require_once('init.php.inc');

if (function_exists('ctype_digit'))
{
	$this->isTrue(ctype_digit('5686541641'),
		_WT('Original ctype_digit fails to validate random numbers.'));

	$this->isFalse(ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_digit returns true for [0-9a-z].'));
	$this->isFalse(ctype_digit('fjsdiopfhsiofnuios'),
		_WT('Original ctype_digit returns true for random letters.'));
	$this->isFalse(ctype_digit('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_digit returns true for random uppercase letters.'));
	$this->isFalse(ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_digit returns true for [0-9a-z?].'));
	$this->isFalse(ctype_digit('5A1C9B3F'),
		_WT('Original ctype_digit returns true for random hexadecimal numbers.'));
	$this->isFalse(ctype_digit('1.5'),
		_WT('Original ctype_digit returns true for a float number.'));
	$this->isFalse(ctype_digit('?*#'),
		_WT('Original ctype_digit returns true for punctuation.'));
	$this->isFalse(ctype_digit("\r\n\t"),
		_WT('Original ctype_digit returns true for control characters.'));
	$this->isFalse(ctype_digit(' '),
		_WT('Original ctype_digit returns true for a space.'));
	$this->isFalse(ctype_digit(''),
		_WT('Original ctype_digit returns true for the empty string.'));
	$this->isFalse(ctype_digit(null),
		_WT('Original ctype_digit returns true for a null value.'));
}

$this->isTrue(emul_ctype_digit('5686541641'),
	_WT('Emulated ctype_digit fails to validate random numbers.'));

$this->isFalse(emul_ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_digit returns true for [0-9a-z].'));
$this->isFalse(emul_ctype_digit('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_digit returns true for random letters.'));
$this->isFalse(emul_ctype_digit('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_digit returns true for random uppercase letters.'));
$this->isFalse(emul_ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_digit returns true for [0-9a-z?].'));
$this->isFalse(emul_ctype_digit('5A1C9B3F'),
	_WT('Emulated ctype_digit returns true for random hexadecimal numbers.'));
$this->isFalse(emul_ctype_digit('1.5'),
	_WT('Emulated ctype_digit returns true for a float number.'));
$this->isFalse(emul_ctype_digit('?*#'),
	_WT('Emulated ctype_digit returns true for punctuation.'));
$this->isFalse(emul_ctype_digit("\r\n\t"),
	_WT('Emulated ctype_digit returns true for control characters.'));
$this->isFalse(emul_ctype_digit(' '),
	_WT('Emulated ctype_digit returns true for a space.'));
$this->isFalse(emul_ctype_digit(''),
	_WT('Emulated ctype_digit returns true for the empty string.'));
$this->isFalse(emul_ctype_digit(null),
	_WT('Emulated ctype_digit returns true for a null value.'));
