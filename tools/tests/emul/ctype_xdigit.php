<?php

require_once('init.php.inc');

if (function_exists('ctype_xdigit'))
{
	$this->isTrue(ctype_xdigit('5686541641'),
		'Original ctype_xdigit fails to validate an hexadecimal number that contains only number characters.');
	$this->isTrue(ctype_xdigit('5A1C9B3F'),
		'Original ctype_xdigit fails to validate an hexadecimal number.');

	$this->isFalse(ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_xdigit returns true for [0-9a-z].');
	$this->isFalse(ctype_xdigit('fjsdiopfhsiofnuios'),
		'Original ctype_xdigit returns true for random letters.');
	$this->isFalse(ctype_xdigit('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_xdigit returns true for random uppercase letters.');
	$this->isFalse(ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_xdigit returns true for [0-9a-z?].');
	$this->isFalse(ctype_xdigit('1.5'),
		'Original ctype_xdigit returns true for a float number.');
	$this->isFalse(ctype_xdigit('?*#'),
		'Original ctype_xdigit returns true for punctuation.');
	$this->isFalse(ctype_xdigit("\r\n\t"),
		'Original ctype_xdigit returns true for control characters.');
	$this->isFalse(ctype_xdigit(' '),
		'Original ctype_xdigit returns true for a space.');
	$this->isFalse(ctype_xdigit(''),
		'Original ctype_xdigit returns true for the empty string.');
	$this->isFalse(ctype_xdigit(null),
		'Original ctype_xdigit returns true for a null value.');
}

$this->isTrue(emul_ctype_xdigit('5686541641'),
	'Emulated ctype_xdigit fails to validate an hexadecimal number that contains only number characters.');
$this->isTrue(emul_ctype_xdigit('5A1C9B3F'),
	'Emulated ctype_xdigit fails to validate an hexadecimal number.');

$this->isFalse(emul_ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_xdigit returns true for [0-9a-z].');
$this->isFalse(emul_ctype_xdigit('fjsdiopfhsiofnuios'),
	'Emulated ctype_xdigit returns true for random letters.');
$this->isFalse(emul_ctype_xdigit('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_xdigit returns true for random uppercase letters.');
$this->isFalse(emul_ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_xdigit returns true for [0-9a-z?].');
$this->isFalse(emul_ctype_xdigit('1.5'),
	'Emulated ctype_xdigit returns true for a float number.');
$this->isFalse(emul_ctype_xdigit('?*#'),
	'Emulated ctype_xdigit returns true for punctuation.');
$this->isFalse(emul_ctype_xdigit("\r\n\t"),
	'Emulated ctype_xdigit returns true for control characters.');
$this->isFalse(emul_ctype_xdigit(' '),
	'Emulated ctype_xdigit returns true for a space.');
$this->isFalse(emul_ctype_xdigit(''),
	'Emulated ctype_xdigit returns true for the empty string.');
$this->isFalse(emul_ctype_xdigit(null),
	'Emulated ctype_xdigit returns true for a null value.');
