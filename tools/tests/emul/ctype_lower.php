<?php

require_once('init.php.inc');

if (function_exists('ctype_lower'))
{
	$this->isTrue(ctype_lower('fjsdiopfhsiofnuios'),
		_WT('Original ctype_lower fails to validate random lowercase letters.'));

	$this->isFalse(ctype_lower('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_lower returns true for random uppercase letters.'));
	$this->isFalse(ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_lower returns true for [0-9a-z].'));
	$this->isFalse(ctype_lower('5686541641'),
		_WT('Original ctype_lower returns true for random numbers.'));
	$this->isFalse(ctype_lower('5A1C9B3F'),
		_WT('Original ctype_lower returns true for random hexadecimal numbers.'));
	$this->isFalse(ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_lower returns true for [0-9a-z?].'));
	$this->isFalse(ctype_lower('1.5'),
		_WT('Original ctype_lower returns true for a float number.'));
	$this->isFalse(ctype_lower('?*#'),
		_WT('Original ctype_lower returns true for punctuation.'));
	$this->isFalse(ctype_lower("\r\n\t"),
		_WT('Original ctype_lower returns true for control characters.'));
	$this->isFalse(ctype_lower(' '),
		_WT('Original ctype_lower returns true for a space.'));
	$this->isFalse(ctype_lower(''),
		_WT('Original ctype_lower returns true for the empty string.'));
	$this->isFalse(ctype_lower(null),
		_WT('Original ctype_lower returns true for a null value.'));
}

$this->isTrue(emul_ctype_lower('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_lower fails to validate random lowercase letters.'));

$this->isFalse(emul_ctype_lower('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_lower returns true for random uppercase letters.'));
$this->isFalse(emul_ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_lower returns true for [0-9a-z].'));
$this->isFalse(emul_ctype_lower('5686541641'),
	_WT('Emulated ctype_lower returns true for random numbers.'));
$this->isFalse(emul_ctype_lower('5A1C9B3F'),
	_WT('Emulated ctype_lower returns true for random hexadecimal numbers.'));
$this->isFalse(emul_ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_lower returns true for [0-9a-z?].'));
$this->isFalse(emul_ctype_lower('1.5'),
	_WT('Emulated ctype_lower returns true for a float number.'));
$this->isFalse(emul_ctype_lower('?*#'),
	_WT('Emulated ctype_lower returns true for punctuation.'));
$this->isFalse(emul_ctype_lower("\r\n\t"),
	_WT('Emulated ctype_lower returns true for control characters.'));
$this->isFalse(emul_ctype_lower(' '),
	_WT('Emulated ctype_lower returns true for a space.'));
$this->isFalse(emul_ctype_lower(''),
	_WT('Emulated ctype_lower returns true for the empty string.'));
$this->isFalse(emul_ctype_lower(null),
	_WT('Emulated ctype_lower returns true for a null value.'));
