<?php

require_once('init.php.inc');

if (function_exists('ctype_digit'))
{
	$this->isTrue(ctype_digit('5686541641'),
		'Original ctype_digit fails to validate random numbers.');

	$this->isFalse(ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_digit returns true for [0-9a-z].');
	$this->isFalse(ctype_digit('fjsdiopfhsiofnuios'),
		'Original ctype_digit returns true for random letters.');
	$this->isFalse(ctype_digit('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_digit returns true for random uppercase letters.');
	$this->isFalse(ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_digit returns true for [0-9a-z?].');
	$this->isFalse(ctype_digit('5A1C9B3F'),
		'Original ctype_digit returns true for random hexadecimal numbers.');
	$this->isFalse(ctype_digit('1.5'),
		'Original ctype_digit returns true for a float number.');
	$this->isFalse(ctype_digit('?*#'),
		'Original ctype_digit returns true for punctuation.');
	$this->isFalse(ctype_digit("\r\n\t"),
		'Original ctype_digit returns true for control characters.');
	$this->isFalse(ctype_digit(' '),
		'Original ctype_digit returns true for a space.');
	$this->isFalse(ctype_digit(''),
		'Original ctype_digit returns true for the empty string.');
	$this->isFalse(ctype_digit(null),
		'Original ctype_digit returns true for a null value.');
}

$this->isTrue(emul_ctype_digit('5686541641'),
	'Emulated ctype_digit fails to validate random numbers.');

$this->isFalse(emul_ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_digit returns true for [0-9a-z].');
$this->isFalse(emul_ctype_digit('fjsdiopfhsiofnuios'),
	'Emulated ctype_digit returns true for random letters.');
$this->isFalse(emul_ctype_digit('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_digit returns true for random uppercase letters.');
$this->isFalse(emul_ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_digit returns true for [0-9a-z?].');
$this->isFalse(emul_ctype_digit('5A1C9B3F'),
	'Emulated ctype_digit returns true for random hexadecimal numbers.');
$this->isFalse(emul_ctype_digit('1.5'),
	'Emulated ctype_digit returns true for a float number.');
$this->isFalse(emul_ctype_digit('?*#'),
	'Emulated ctype_digit returns true for punctuation.');
$this->isFalse(emul_ctype_digit("\r\n\t"),
	'Emulated ctype_digit returns true for control characters.');
$this->isFalse(emul_ctype_digit(' '),
	'Emulated ctype_digit returns true for a space.');
$this->isFalse(emul_ctype_digit(''),
	'Emulated ctype_digit returns true for the empty string.');
$this->isFalse(emul_ctype_digit(null),
	'Emulated ctype_digit returns true for a null value.');
