<?php

require_once('init.php.inc');

if (function_exists('ctype_cntrl'))
{
	$this->isTrue(ctype_cntrl("\r\n\t"),
		_WT('Original ctype_cntrl fails to validate control characters.'));

	$this->isFalse(ctype_cntrl('fjsdiopfhsiofnuios'),
		_WT('Original ctype_cntrl returns true for random letters.'));
	$this->isFalse(ctype_cntrl('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_cntrl returns true for random uppercase letters.'));
	$this->isFalse(ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_cntrl returns true for [0-9a-z].'));
	$this->isFalse(ctype_cntrl('5686541641'),
		_WT('Original ctype_cntrl returns true for random numbers.'));
	$this->isFalse(ctype_cntrl('5A1C9B3F'),
		_WT('Original ctype_cntrl returns true for random hexadecimal numbers.'));
	$this->isFalse(ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_cntrl returns true for [0-9a-z?].'));
	$this->isFalse(ctype_cntrl('1.5'),
		_WT('Original ctype_cntrl returns true for a float number.'));
	$this->isFalse(ctype_cntrl('?*#'),
		_WT('Original ctype_cntrl returns true for punctuation.'));
	$this->isFalse(ctype_cntrl(' '),
		_WT('Original ctype_cntrl returns true for a space.'));
	$this->isFalse(ctype_cntrl(''),
		_WT('Original ctype_cntrl returns true for the empty string.'));
	$this->isFalse(ctype_cntrl(null),
		_WT('Original ctype_cntrl returns true for a null value.'));
}

$this->isTrue(emul_ctype_cntrl("\r\n\t"),
	_WT('Emulated ctype_cntrl fails to validate control characters.'));

$this->isFalse(emul_ctype_cntrl('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_cntrl returns true for random letters.'));
$this->isFalse(emul_ctype_cntrl('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_cntrl returns true for random uppercase letters.'));
$this->isFalse(emul_ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_cntrl returns true for [0-9a-z].'));
$this->isFalse(emul_ctype_cntrl('5686541641'),
	_WT('Emulated ctype_cntrl returns true for random numbers.'));
$this->isFalse(emul_ctype_cntrl('5A1C9B3F'),
	_WT('Emulated ctype_cntrl returns true for random hexadecimal numbers.'));
$this->isFalse(emul_ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_cntrl returns true for [0-9a-z?].'));
$this->isFalse(emul_ctype_cntrl('1.5'),
	_WT('Emulated ctype_cntrl returns true for a float number.'));
$this->isFalse(emul_ctype_cntrl('?*#'),
	_WT('Emulated ctype_cntrl returns true for punctuation.'));
$this->isFalse(emul_ctype_cntrl(' '),
	_WT('Emulated ctype_cntrl returns true for a space.'));
$this->isFalse(emul_ctype_cntrl(''),
	_WT('Emulated ctype_cntrl returns true for the empty string.'));
$this->isFalse(emul_ctype_cntrl(null),
	_WT('Emulated ctype_cntrl returns true for a null value.'));
