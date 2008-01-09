<?php

require_once('init.php.inc');

if (function_exists('ctype_space'))
{
	$this->isTrue(ctype_space("\r\n\t"),
		'Original ctype_space fails to validate newline and tabulation control characters.');
	$this->isTrue(ctype_space(' '),
		'Original ctype_space fails to validate a space.');

	$this->isFalse(ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_space returns true for [0-9a-z].');
	$this->isFalse(ctype_space('5686541641'),
		'Original ctype_space returns true for random numbers.');
	$this->isFalse(ctype_space('5A1C9B3F'),
		'Original ctype_space returns true for random hexadecimal numbers.');
	$this->isFalse(ctype_space('fjsdiopfhsiofnuios'),
		'Original ctype_space returns true for random letters.');
	$this->isFalse(ctype_space('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_space returns true for random uppercase letters.');
	$this->isFalse(ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_space returns true for [0-9a-z?].');
	$this->isFalse(ctype_space('1.5'),
		'Original ctype_space returns true for a float number.');
	$this->isFalse(ctype_space('?*#'),
		'Original ctype_space returns true for punctuation.');
	$this->isFalse(ctype_space(''),
		'Original ctype_space returns true for the empty string.');
	$this->isFalse(ctype_space(null),
		'Original ctype_space returns true for a null value.');
}

$this->isTrue(emul_ctype_space("\r\n\t"),
	'Emulated ctype_space fails to validate newline and tabulation control characters.');
$this->isTrue(emul_ctype_space(' '),
	'Emulated ctype_space fails to validate a space.');

$this->isFalse(emul_ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_space returns true for [0-9a-z].');
$this->isFalse(emul_ctype_space('5686541641'),
	'Emulated ctype_space returns true for random numbers.');
$this->isFalse(emul_ctype_space('5A1C9B3F'),
	'Emulated ctype_space returns true for random hexadecimal numbers.');
$this->isFalse(emul_ctype_space('fjsdiopfhsiofnuios'),
	'Emulated ctype_space returns true for random letters.');
$this->isFalse(emul_ctype_space('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_space returns true for random uppercase letters.');
$this->isFalse(emul_ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_space returns true for [0-9a-z?].');
$this->isFalse(emul_ctype_space('1.5'),
	'Emulated ctype_space returns true for a float number.');
$this->isFalse(emul_ctype_space('?*#'),
	'Emulated ctype_space returns true for punctuation.');
$this->isFalse(emul_ctype_space(''),
	'Emulated ctype_space returns true for the empty string.');
$this->isFalse(emul_ctype_space(null),
	'Emulated ctype_space returns true for a null value.');
