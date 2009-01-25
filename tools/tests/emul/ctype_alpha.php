<?php

require_once('init.php.inc');

if (function_exists('ctype_alpha'))
{
	$this->isTrue(ctype_alpha('fjsdiopfhsiofnuios'),
		_WT('Original ctype_alpha fails to validate random letters.'));
	$this->isTrue(ctype_alpha('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_alpha fails to validate random uppercase letters.'));

	$this->isFalse(ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_alpha returns true for [0-9a-z].'));
	$this->isFalse(ctype_alpha('5686541641'),
		_WT('Original ctype_alpha returns true for random numbers.'));
	$this->isFalse(ctype_alpha('5A1C9B3F'),
		_WT('Original ctype_alpha returns true for random hexadecimal numbers.'));
	$this->isFalse(ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_alpha returns true for [0-9a-z?].'));
	$this->isFalse(ctype_alpha('1.5'),
		_WT('Original ctype_alpha returns true for a float number.'));
	$this->isFalse(ctype_alpha('?*#'),
		_WT('Original ctype_alpha returns true for punctuation.'));
	$this->isFalse(ctype_alpha("\r\n\t"),
		_WT('Original ctype_alpha returns true for control characters.'));
	$this->isFalse(ctype_alpha(' '),
		_WT('Original ctype_alpha returns true for a space.'));
	$this->isFalse(ctype_alpha(''),
		_WT('Original ctype_alpha returns true for the empty string.'));
	$this->isFalse(ctype_alpha(null),
		_WT('Original ctype_alpha returns true for a null value.'));
}

$this->isTrue(emul_ctype_alpha('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_alpha fails to validate random letters.'));
$this->isTrue(emul_ctype_alpha('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_alpha fails to validate random uppercase letters.'));

$this->isFalse(emul_ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_alpha returns true for [0-9a-z].'));
$this->isFalse(emul_ctype_alpha('5686541641'),
	_WT('Emulated ctype_alpha returns true for random numbers.'));
$this->isFalse(emul_ctype_alpha('5A1C9B3F'),
	_WT('Emulated ctype_alpha returns true for random hexadecimal numbers.'));
$this->isFalse(emul_ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_alpha returns true for [0-9a-z?].'));
$this->isFalse(emul_ctype_alpha('1.5'),
	_WT('Emulated ctype_alpha returns true for a float number.'));
$this->isFalse(emul_ctype_alpha('?*#'),
	_WT('Emulated ctype_alpha returns true for punctuation.'));
$this->isFalse(emul_ctype_alpha("\r\n\t"),
	_WT('Emulated ctype_alpha returns true for control characters.'));
$this->isFalse(emul_ctype_alpha(' '),
	_WT('Emulated ctype_alpha returns true for a space.'));
$this->isFalse(emul_ctype_alpha(''),
	_WT('Emulated ctype_alpha returns true for the empty string.'));
$this->isFalse(emul_ctype_alpha(null),
	_WT('Emulated ctype_alpha returns true for a null value.'));
