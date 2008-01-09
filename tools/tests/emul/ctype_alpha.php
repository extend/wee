<?php

require_once('init.php.inc');

if (function_exists('ctype_alpha'))
{
	$this->isTrue(ctype_alpha('fjsdiopfhsiofnuios'),
		'Original ctype_alpha fails to validate random letters.');
	$this->isTrue(ctype_alpha('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_alpha fails to validate random uppercase letters.');

	$this->isFalse(ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_alpha returns true for [0-9a-z].');
	$this->isFalse(ctype_alpha('5686541641'),
		'Original ctype_alpha returns true for random numbers.');
	$this->isFalse(ctype_alpha('5A1C9B3F'),
		'Original ctype_alpha returns true for random hexadecimal numbers.');
	$this->isFalse(ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_alpha returns true for [0-9a-z?].');
	$this->isFalse(ctype_alpha('1.5'),
		'Original ctype_alpha returns true for a float number.');
	$this->isFalse(ctype_alpha('?*#'),
		'Original ctype_alpha returns true for punctuation.');
	$this->isFalse(ctype_alpha("\r\n\t"),
		'Original ctype_alpha returns true for control characters.');
	$this->isFalse(ctype_alpha(' '),
		'Original ctype_alpha returns true for a space.');
	$this->isFalse(ctype_alpha(''),
		'Original ctype_alpha returns true for the empty string.');
	$this->isFalse(ctype_alpha(null),
		'Original ctype_alpha returns true for a null value.');
}

$this->isTrue(emul_ctype_alpha('fjsdiopfhsiofnuios'),
	'Emulated ctype_alpha fails to validate random letters.');
$this->isTrue(emul_ctype_alpha('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_alpha fails to validate random uppercase letters.');

$this->isFalse(emul_ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_alpha returns true for [0-9a-z].');
$this->isFalse(emul_ctype_alpha('5686541641'),
	'Emulated ctype_alpha returns true for random numbers.');
$this->isFalse(emul_ctype_alpha('5A1C9B3F'),
	'Emulated ctype_alpha returns true for random hexadecimal numbers.');
$this->isFalse(emul_ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_alpha returns true for [0-9a-z?].');
$this->isFalse(emul_ctype_alpha('1.5'),
	'Emulated ctype_alpha returns true for a float number.');
$this->isFalse(emul_ctype_alpha('?*#'),
	'Emulated ctype_alpha returns true for punctuation.');
$this->isFalse(emul_ctype_alpha("\r\n\t"),
	'Emulated ctype_alpha returns true for control characters.');
$this->isFalse(emul_ctype_alpha(' '),
	'Emulated ctype_alpha returns true for a space.');
$this->isFalse(emul_ctype_alpha(''),
	'Emulated ctype_alpha returns true for the empty string.');
$this->isFalse(emul_ctype_alpha(null),
	'Emulated ctype_alpha returns true for a null value.');
