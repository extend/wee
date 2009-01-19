<?php

require_once('init.php.inc');

if (function_exists('ctype_print'))
{
	$this->isTrue(ctype_print('fjsdiopfhsiofnuios'),
		_WT('Original ctype_print fails to validate random letters.'));
	$this->isTrue(ctype_print('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_print fails to validate random uppercase letters.'));
	$this->isTrue(ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_print fails to validate [0-9a-z].'));
	$this->isTrue(ctype_print('5686541641'),
		_WT('Original ctype_print fails to validate random numbers.'));
	$this->isTrue(ctype_print('5A1C9B3F'),
		_WT('Original ctype_print fails to validate random hexadecimal numbers.'));
	$this->isTrue(ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_print fails to validate [0-9a-z?].'));
	$this->isTrue(ctype_print('1.5'),
		_WT('Original ctype_print fails to validate a float number.'));
	$this->isTrue(ctype_print('?*#'),
		_WT('Original ctype_print fails to validate punctuation.'));
	$this->isTrue(ctype_print(' '),
		_WT('Original ctype_print fails to validate a space.'));

	$this->isFalse(ctype_print("\r\n\t"),
		_WT('Original ctype_print returns true for control characters.'));
	$this->isFalse(ctype_print(''),
		_WT('Original ctype_print returns true for the empty string.'));
	$this->isFalse(ctype_print(null),
		_WT('Original ctype_print returns true for a null value.'));
}

$this->isTrue(emul_ctype_print('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_print fails to validate random letters.'));
$this->isTrue(emul_ctype_print('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_print fails to validate random uppercase letters.'));
$this->isTrue(emul_ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_print fails to validate [0-9a-z].'));
$this->isTrue(emul_ctype_print('5686541641'),
	_WT('Emulated ctype_print fails to validate random numbers.'));
$this->isTrue(emul_ctype_print('5A1C9B3F'),
	_WT('Emulated ctype_print fails to validate random hexadecimal numbers.'));
$this->isTrue(emul_ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_print fails to validate [0-9a-z?].'));
$this->isTrue(emul_ctype_print('1.5'),
	_WT('Emulated ctype_print fails to validate a float number.'));
$this->isTrue(emul_ctype_print('?*#'),
	_WT('Emulated ctype_print fails to validate punctuation.'));
$this->isTrue(emul_ctype_print(' '),
	_WT('Emulated ctype_print fails to validate a space.'));

$this->isFalse(emul_ctype_print("\r\n\t"),
	_WT('Emulated ctype_print returns true for control characters.'));
$this->isFalse(emul_ctype_print(''),
	_WT('Emulated ctype_print returns true for the empty string.'));
$this->isFalse(emul_ctype_print(null),
	_WT('Emulated ctype_print returns true for a null value.'));
