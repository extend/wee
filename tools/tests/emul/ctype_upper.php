<?php

require_once('init.php.inc');

if (function_exists('ctype_upper'))
{
	$this->isTrue(ctype_upper('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_upper fails to validate uppercase letters.'));

	$this->isFalse(ctype_upper('fjsdiopfhsiofnuios'),
		_WT('Original ctype_upper returns true for random letters.'));
	$this->isFalse(ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_upper returns true for [0-9a-z].'));
	$this->isFalse(ctype_upper('5686541641'),
		_WT('Original ctype_upper returns true for random numbers.'));
	$this->isFalse(ctype_upper('5A1C9B3F'),
		_WT('Original ctype_upper returns true for random hexadecimal numbers.'));
	$this->isFalse(ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_upper returns true for [0-9a-z?].'));
	$this->isFalse(ctype_upper('1.5'),
		_WT('Original ctype_upper returns true for a float number.'));
	$this->isFalse(ctype_upper('?*#'),
		_WT('Original ctype_upper returns true for punctuation.'));
	$this->isFalse(ctype_upper("\r\n\t"),
		_WT('Original ctype_upper returns true for control characters.'));
	$this->isFalse(ctype_upper(' '),
		_WT('Original ctype_upper returns true for a space.'));
	$this->isFalse(ctype_upper(''),
		_WT('Original ctype_upper returns true for the empty string.'));
	$this->isFalse(ctype_upper(null),
		_WT('Original ctype_upper returns true for a null value.'));
}

$this->isTrue(emul_ctype_upper('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_upper fails to validate uppercase letters.'));

$this->isFalse(emul_ctype_upper('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_upper returns true for random letters.'));
$this->isFalse(emul_ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_upper returns true for [0-9a-z].'));
$this->isFalse(emul_ctype_upper('5686541641'),
	_WT('Emulated ctype_upper returns true for random numbers.'));
$this->isFalse(emul_ctype_upper('5A1C9B3F'),
	_WT('Emulated ctype_upper returns true for random hexadecimal numbers.'));
$this->isFalse(emul_ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_upper returns true for [0-9a-z?].'));
$this->isFalse(emul_ctype_upper('1.5'),
	_WT('Emulated ctype_upper returns true for a float number.'));
$this->isFalse(emul_ctype_upper('?*#'),
	_WT('Emulated ctype_upper returns true for punctuation.'));
$this->isFalse(emul_ctype_upper("\r\n\t"),
	_WT('Emulated ctype_upper returns true for control characters.'));
$this->isFalse(emul_ctype_upper(' '),
	_WT('Emulated ctype_upper returns true for a space.'));
$this->isFalse(emul_ctype_upper(''),
	_WT('Emulated ctype_upper returns true for the empty string.'));
$this->isFalse(emul_ctype_upper(null),
	_WT('Emulated ctype_upper returns true for a null value.'));
