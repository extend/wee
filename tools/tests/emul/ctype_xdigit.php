<?php

require_once('init.php.inc');

if (function_exists('ctype_xdigit'))
{
	$this->isTrue(ctype_xdigit('5686541641'),
		_WT('Original ctype_xdigit fails to validate an hexadecimal number that contains only number characters.'));
	$this->isTrue(ctype_xdigit('5A1C9B3F'),
		_WT('Original ctype_xdigit fails to validate an hexadecimal number.'));

	$this->isFalse(ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_xdigit returns true for [0-9a-z].'));
	$this->isFalse(ctype_xdigit('fjsdiopfhsiofnuios'),
		_WT('Original ctype_xdigit returns true for random letters.'));
	$this->isFalse(ctype_xdigit('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_xdigit returns true for random uppercase letters.'));
	$this->isFalse(ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_xdigit returns true for [0-9a-z?].'));
	$this->isFalse(ctype_xdigit('1.5'),
		_WT('Original ctype_xdigit returns true for a float number.'));
	$this->isFalse(ctype_xdigit('?*#'),
		_WT('Original ctype_xdigit returns true for punctuation.'));
	$this->isFalse(ctype_xdigit("\r\n\t"),
		_WT('Original ctype_xdigit returns true for control characters.'));
	$this->isFalse(ctype_xdigit(' '),
		_WT('Original ctype_xdigit returns true for a space.'));
	$this->isFalse(ctype_xdigit(''),
		_WT('Original ctype_xdigit returns true for the empty string.'));
	$this->isFalse(ctype_xdigit(null),
		_WT('Original ctype_xdigit returns true for a null value.'));
}

$this->isTrue(emul_ctype_xdigit('5686541641'),
	_WT('Emulated ctype_xdigit fails to validate an hexadecimal number that contains only number characters.'));
$this->isTrue(emul_ctype_xdigit('5A1C9B3F'),
	_WT('Emulated ctype_xdigit fails to validate an hexadecimal number.'));

$this->isFalse(emul_ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_xdigit returns true for [0-9a-z].'));
$this->isFalse(emul_ctype_xdigit('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_xdigit returns true for random letters.'));
$this->isFalse(emul_ctype_xdigit('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_xdigit returns true for random uppercase letters.'));
$this->isFalse(emul_ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_xdigit returns true for [0-9a-z?].'));
$this->isFalse(emul_ctype_xdigit('1.5'),
	_WT('Emulated ctype_xdigit returns true for a float number.'));
$this->isFalse(emul_ctype_xdigit('?*#'),
	_WT('Emulated ctype_xdigit returns true for punctuation.'));
$this->isFalse(emul_ctype_xdigit("\r\n\t"),
	_WT('Emulated ctype_xdigit returns true for control characters.'));
$this->isFalse(emul_ctype_xdigit(' '),
	_WT('Emulated ctype_xdigit returns true for a space.'));
$this->isFalse(emul_ctype_xdigit(''),
	_WT('Emulated ctype_xdigit returns true for the empty string.'));
$this->isFalse(emul_ctype_xdigit(null),
	_WT('Emulated ctype_xdigit returns true for a null value.'));
