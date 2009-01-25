<?php

require_once('init.php.inc');

if (function_exists('ctype_space'))
{
	$this->isTrue(ctype_space("\r\n\t"),
		_WT('Original ctype_space fails to validate newline and tabulation control characters.'));
	$this->isTrue(ctype_space(' '),
		_WT('Original ctype_space fails to validate a space.'));

	$this->isFalse(ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_space returns true for [0-9a-z].'));
	$this->isFalse(ctype_space('5686541641'),
		_WT('Original ctype_space returns true for random numbers.'));
	$this->isFalse(ctype_space('5A1C9B3F'),
		_WT('Original ctype_space returns true for random hexadecimal numbers.'));
	$this->isFalse(ctype_space('fjsdiopfhsiofnuios'),
		_WT('Original ctype_space returns true for random letters.'));
	$this->isFalse(ctype_space('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_space returns true for random uppercase letters.'));
	$this->isFalse(ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_space returns true for [0-9a-z?].'));
	$this->isFalse(ctype_space('1.5'),
		_WT('Original ctype_space returns true for a float number.'));
	$this->isFalse(ctype_space('?*#'),
		_WT('Original ctype_space returns true for punctuation.'));
	$this->isFalse(ctype_space(''),
		_WT('Original ctype_space returns true for the empty string.'));
	$this->isFalse(ctype_space(null),
		_WT('Original ctype_space returns true for a null value.'));
}

$this->isTrue(emul_ctype_space("\r\n\t"),
	_WT('Emulated ctype_space fails to validate newline and tabulation control characters.'));
$this->isTrue(emul_ctype_space(' '),
	_WT('Emulated ctype_space fails to validate a space.'));

$this->isFalse(emul_ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_space returns true for [0-9a-z].'));
$this->isFalse(emul_ctype_space('5686541641'),
	_WT('Emulated ctype_space returns true for random numbers.'));
$this->isFalse(emul_ctype_space('5A1C9B3F'),
	_WT('Emulated ctype_space returns true for random hexadecimal numbers.'));
$this->isFalse(emul_ctype_space('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_space returns true for random letters.'));
$this->isFalse(emul_ctype_space('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_space returns true for random uppercase letters.'));
$this->isFalse(emul_ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_space returns true for [0-9a-z?].'));
$this->isFalse(emul_ctype_space('1.5'),
	_WT('Emulated ctype_space returns true for a float number.'));
$this->isFalse(emul_ctype_space('?*#'),
	_WT('Emulated ctype_space returns true for punctuation.'));
$this->isFalse(emul_ctype_space(''),
	_WT('Emulated ctype_space returns true for the empty string.'));
$this->isFalse(emul_ctype_space(null),
	_WT('Emulated ctype_space returns true for a null value.'));
