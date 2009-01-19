<?php

require_once('init.php.inc');

if (function_exists('ctype_graph'))
{
	$this->isTrue(ctype_graph('fjsdiopfhsiofnuios'),
		_WT('Original ctype_graph fails to validate random letters.'));
	$this->isTrue(ctype_graph('FELMNFKLFDSNFSKLFNSDL'),
		_WT('Original ctype_graph fails to validate random uppercase letters.'));
	$this->isTrue(ctype_graph('0123456789azertyuiopqsdfghjklmwxcvbn'),
		_WT('Original ctype_graph fails to validate [0-9a-z].'));
	$this->isTrue(ctype_graph('5686541641'),
		_WT('Original ctype_graph fails to validate random numbers.'));
	$this->isTrue(ctype_graph('5A1C9B3F'),
		_WT('Original ctype_graph fails to validate random hexadecimal numbers.'));
	$this->isTrue(ctype_graph('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		_WT('Original ctype_graph fails to validate [0-9a-z?].'));
	$this->isTrue(ctype_graph('1.5'),
		_WT('Original ctype_graph fails to validate a float number.'));
	$this->isTrue(ctype_graph('?*#'),
		_WT('Original ctype_graph fails to validate punctuation.'));

	$this->isFalse(ctype_graph("\r\n\t"),
		_WT('Original ctype_graph returns true for control characters.'));
	$this->isFalse(ctype_graph(' '),
		_WT('Original ctype_graph returns true for a space.'));
	$this->isFalse(ctype_graph(''),
		_WT('Original ctype_graph returns true for the empty string.'));
	$this->isFalse(ctype_graph(null),
		_WT('Original ctype_graph returns true for a null value.'));
}

$this->isTrue(emul_ctype_graph('fjsdiopfhsiofnuios'),
	_WT('Emulated ctype_graph fails to validate random letters.'));
$this->isTrue(emul_ctype_graph('FELMNFKLFDSNFSKLFNSDL'),
	_WT('Emulated ctype_graph fails to validate random uppercase letters.'));
$this->isTrue(emul_ctype_graph('0123456789azertyuiopqsdfghjklmwxcvbn'),
	_WT('Emulated ctype_graph fails to validate [0-9a-z].'));
$this->isTrue(emul_ctype_graph('5686541641'),
	_WT('Emulated ctype_graph fails to validate random numbers.'));
$this->isTrue(emul_ctype_graph('5A1C9B3F'),
	_WT('Emulated ctype_graph fails to validate random hexadecimal numbers.'));
$this->isTrue(emul_ctype_graph('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	_WT('Emulated ctype_graph fails to validate [0-9a-z?].'));
$this->isTrue(emul_ctype_graph('1.5'),
	_WT('Emulated ctype_graph fails to validate a float number.'));
$this->isTrue(emul_ctype_graph('?*#'),
	_WT('Emulated ctype_graph fails to validate punctuation.'));

$this->isFalse(emul_ctype_graph("\r\n\t"),
	_WT('Emulated ctype_graph returns true for control characters.'));
$this->isFalse(emul_ctype_graph(' '),
	_WT('Emulated ctype_graph returns true for a space.'));
$this->isFalse(emul_ctype_graph(''),
	_WT('Emulated ctype_graph returns true for the empty string.'));
$this->isFalse(emul_ctype_graph(null),
	_WT('Emulated ctype_graph returns true for a null value.'));
