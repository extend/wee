<?php

require_once('init.php.inc');

if (function_exists('ctype_alnum'))
{
	$this->isTrue(ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_alnum fails to validate [0-9a-z].'));
	$this->isTrue(ctype_alnum('5686541641'),
		_WT('Original ctype_alnum fails to validate random numbers.'));
	$this->isTrue(ctype_alnum('5A1C9B3F'),
		_WT('Original ctype_alnum fails to validate random hexadecimal numbers.'));
	$this->isTrue(ctype_alnum('fjsdiopfhsiofnuios'),
		_WT('Original ctype_alnum fails to validate random letters.'));
	$this->isTrue(ctype_alnum('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_alnum fails to validate random uppercase letters.'));

	$this->isFalse(ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_alnum returns true for [0-9a-z?].'));
	$this->isFalse(ctype_alnum('1.5'),
		_WT('Original ctype_alnum returns true for a float number.'));
	$this->isFalse(ctype_alnum('?*#'),
		_WT('Original ctype_alnum returns true for punctuation.'));
	$this->isFalse(ctype_alnum("\r\n\t"),
		_WT('Original ctype_alnum returns true for control characters.'));
	$this->isFalse(ctype_alnum(' '),
		_WT('Original ctype_alnum returns true for a space.'));
	$this->isFalse(ctype_alnum(''),
		_WT('Original ctype_alnum returns true for the empty string.'));
	$this->isFalse(ctype_alnum(null),
		_WT('Original ctype_alnum returns true for a null value.'));
}

$this->isTrue(emul_ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_alnum fails to validate [0-9a-z].'));
$this->isTrue(emul_ctype_alnum('5686541641'),
	_WT('Emulated ctype_alnum fails to validate random numbers.'));
$this->isTrue(emul_ctype_alnum('5A1C9B3F'),
	_WT('Emulated ctype_alnum fails to validate random hexadecimal numbers.'));
$this->isTrue(emul_ctype_alnum('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_alnum fails to validate random letters.'));
$this->isTrue(emul_ctype_alnum('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_alnum fails to validate random uppercase letters.'));

$this->isFalse(emul_ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_alnum returns true for [0-9a-z?].'));
$this->isFalse(emul_ctype_alnum('1.5'),
	_WT('Emulated ctype_alnum returns true for a float number.'));
$this->isFalse(emul_ctype_alnum('?*#'),
	_WT('Emulated ctype_alnum returns true for punctuation.'));
$this->isFalse(emul_ctype_alnum("\r\n\t"),
	_WT('Emulated ctype_alnum returns true for control characters.'));
$this->isFalse(emul_ctype_alnum(' '),
	_WT('Emulated ctype_alnum returns true for a space.'));
$this->isFalse(emul_ctype_alnum(''),
	_WT('Emulated ctype_alnum returns true for the empty string.'));
$this->isFalse(emul_ctype_alnum(null),
	_WT('Emulated ctype_alnum returns true for a null value.'));
