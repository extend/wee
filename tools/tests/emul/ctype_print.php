<?php

require_once('init.php.inc');

if (function_exists('ctype_print'))
{
	$this->isTrue(ctype_print('fjsdiopfhsiofnuios'),
		'Original ctype_print fails to validate random letters.');
	$this->isTrue(ctype_print('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_print fails to validate random uppercase letters.');
	$this->isTrue(ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_print fails to validate [0-9a-z].');
	$this->isTrue(ctype_print('5686541641'),
		'Original ctype_print fails to validate random numbers.');
	$this->isTrue(ctype_print('5A1C9B3F'),
		'Original ctype_print fails to validate random hexadecimal numbers.');
	$this->isTrue(ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_print fails to validate [0-9a-z?].');
	$this->isTrue(ctype_print('1.5'),
		'Original ctype_print fails to validate a float number.');
	$this->isTrue(ctype_print('?*#'),
		'Original ctype_print fails to validate punctuation.');
	$this->isTrue(ctype_print(' '),
		'Original ctype_print fails to validate a space.');

	$this->isFalse(ctype_print("\r\n\t"),
		'Original ctype_print returns true for control characters.');
	$this->isFalse(ctype_print(''),
		'Original ctype_print returns true for the empty string.');
	$this->isFalse(ctype_print(null),
		'Original ctype_print returns true for a null value.');
}

$this->isTrue(emul_ctype_print('fjsdiopfhsiofnuios'),
	'Emulated ctype_print fails to validate random letters.');
$this->isTrue(emul_ctype_print('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_print fails to validate random uppercase letters.');
$this->isTrue(emul_ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_print fails to validate [0-9a-z].');
$this->isTrue(emul_ctype_print('5686541641'),
	'Emulated ctype_print fails to validate random numbers.');
$this->isTrue(emul_ctype_print('5A1C9B3F'),
	'Emulated ctype_print fails to validate random hexadecimal numbers.');
$this->isTrue(emul_ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_print fails to validate [0-9a-z?].');
$this->isTrue(emul_ctype_print('1.5'),
	'Emulated ctype_print fails to validate a float number.');
$this->isTrue(emul_ctype_print('?*#'),
	'Emulated ctype_print fails to validate punctuation.');
$this->isTrue(emul_ctype_print(' '),
	'Emulated ctype_print fails to validate a space.');

$this->isFalse(emul_ctype_print("\r\n\t"),
	'Emulated ctype_print returns true for control characters.');
$this->isFalse(emul_ctype_print(''),
	'Emulated ctype_print returns true for the empty string.');
$this->isFalse(emul_ctype_print(null),
	'Emulated ctype_print returns true for a null value.');
