<?php

require_once('init.php.inc');

if (function_exists('ctype_punct'))
{
	$this->isTrue(ctype_punct('?*#'),
		_WT('Original ctype_punct fails to validate punctuation.'));

	$this->isFalse(ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_punct returns true for [0-9a-z].'));
	$this->isFalse(ctype_punct('5686541641'),
		_WT('Original ctype_punct returns true for random numbers.'));
	$this->isFalse(ctype_punct('5A1C9B3F'),
		_WT('Original ctype_punct returns true for random hexadecimal numbers.'));
	$this->isFalse(ctype_punct('fjsdiopfhsiofnuios'),
		_WT('Original ctype_punct returns true for random letters.'));
	$this->isFalse(ctype_punct('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_punct returns true for random uppercase letters.'));
	$this->isFalse(ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_punct returns true for [0-9a-z?].'));
	$this->isFalse(ctype_punct('1.5'),
		_WT('Original ctype_punct returns true for a float number.'));
	$this->isFalse(ctype_punct("\r\n\t"),
		_WT('Original ctype_punct returns true for control characters.'));
	$this->isFalse(ctype_punct(' '),
		_WT('Original ctype_punct returns true for a space.'));
	$this->isFalse(ctype_punct(''),
		_WT('Original ctype_punct returns true for the empty string.'));
	$this->isFalse(ctype_punct(null),
		_WT('Original ctype_punct returns true for a null value.'));
}

$this->isTrue(emul_ctype_punct('?*#'),
	_WT('Emulated ctype_punct fails to validate punctuation.'));

$this->isFalse(emul_ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_punct returns true for [0-9a-z].'));
$this->isFalse(emul_ctype_punct('5686541641'),
	_WT('Emulated ctype_punct returns true for random numbers.'));
$this->isFalse(emul_ctype_punct('5A1C9B3F'),
	_WT('Emulated ctype_punct returns true for random hexadecimal numbers.'));
$this->isFalse(emul_ctype_punct('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_punct returns true for random letters.'));
$this->isFalse(emul_ctype_punct('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_punct returns true for random uppercase letters.'));
$this->isFalse(emul_ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_punct returns true for [0-9a-z?].'));
$this->isFalse(emul_ctype_punct('1.5'),
	_WT('Emulated ctype_punct returns true for a float number.'));
$this->isFalse(emul_ctype_punct("\r\n\t"),
	_WT('Emulated ctype_punct returns true for control characters.'));
$this->isFalse(emul_ctype_punct(' '),
	_WT('Emulated ctype_punct returns true for a space.'));
$this->isFalse(emul_ctype_punct(''),
	_WT('Emulated ctype_punct returns true for the empty string.'));
$this->isFalse(emul_ctype_punct(null),
	_WT('Emulated ctype_punct returns true for a null value.'));
