<?php

require_once('init.php.inc');

if (function_exists('ctype_upper'))
{
	$this->isTrue(ctype_upper('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_upper fails to validate uppercase letters.');

	$this->isFalse(ctype_upper('fjsdiopfhsiofnuios'),
		'Original ctype_upper returns true for random letters.');
	$this->isFalse(ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_upper returns true for [0-9a-z].');
	$this->isFalse(ctype_upper('5686541641'),
		'Original ctype_upper returns true for random numbers.');
	$this->isFalse(ctype_upper('5A1C9B3F'),
		'Original ctype_upper returns true for random hexadecimal numbers.');
	$this->isFalse(ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_upper returns true for [0-9a-z?].');
	$this->isFalse(ctype_upper('1.5'),
		'Original ctype_upper returns true for a float number.');
	$this->isFalse(ctype_upper('?*#'),
		'Original ctype_upper returns true for punctuation.');
	$this->isFalse(ctype_upper("\r\n\t"),
		'Original ctype_upper returns true for control characters.');
	$this->isFalse(ctype_upper(' '),
		'Original ctype_upper returns true for a space.');
	$this->isFalse(ctype_upper(''),
		'Original ctype_upper returns true for the empty string.');
	$this->isFalse(ctype_upper(null),
		'Original ctype_upper returns true for a null value.');
}

$this->isTrue(emul_ctype_upper('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_upper fails to validate uppercase letters.');

$this->isFalse(emul_ctype_upper('fjsdiopfhsiofnuios'),
	'Emulated ctype_upper returns true for random letters.');
$this->isFalse(emul_ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_upper returns true for [0-9a-z].');
$this->isFalse(emul_ctype_upper('5686541641'),
	'Emulated ctype_upper returns true for random numbers.');
$this->isFalse(emul_ctype_upper('5A1C9B3F'),
	'Emulated ctype_upper returns true for random hexadecimal numbers.');
$this->isFalse(emul_ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_upper returns true for [0-9a-z?].');
$this->isFalse(emul_ctype_upper('1.5'),
	'Emulated ctype_upper returns true for a float number.');
$this->isFalse(emul_ctype_upper('?*#'),
	'Emulated ctype_upper returns true for punctuation.');
$this->isFalse(emul_ctype_upper("\r\n\t"),
	'Emulated ctype_upper returns true for control characters.');
$this->isFalse(emul_ctype_upper(' '),
	'Emulated ctype_upper returns true for a space.');
$this->isFalse(emul_ctype_upper(''),
	'Emulated ctype_upper returns true for the empty string.');
$this->isFalse(emul_ctype_upper(null),
	'Emulated ctype_upper returns true for a null value.');
