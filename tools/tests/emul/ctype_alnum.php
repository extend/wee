<?php

require_once('init.php.inc');

if (function_exists('ctype_alnum'))
{
	$this->isTrue(ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_alnum fails to validate [0-9a-z].');
	$this->isTrue(ctype_alnum('5686541641'),
		'Original ctype_alnum fails to validate random numbers.');
	$this->isTrue(ctype_alnum('5A1C9B3F'),
		'Original ctype_alnum fails to validate random hexadecimal numbers.');
	$this->isTrue(ctype_alnum('fjsdiopfhsiofnuios'),
		'Original ctype_alnum fails to validate random letters.');
	$this->isTrue(ctype_alnum('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_alnum fails to validate random uppercase letters.');

	$this->isFalse(ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_alnum returns true for [0-9a-z?].');
	$this->isFalse(ctype_alnum('1.5'),
		'Original ctype_alnum returns true for a float number.');
	$this->isFalse(ctype_alnum('?*#'),
		'Original ctype_alnum returns true for punctuation.');
	$this->isFalse(ctype_alnum("\r\n\t"),
		'Original ctype_alnum returns true for control characters.');
	$this->isFalse(ctype_alnum(' '),
		'Original ctype_alnum returns true for a space.');
	$this->isFalse(ctype_alnum(''),
		'Original ctype_alnum returns true for the empty string.');
	$this->isFalse(ctype_alnum(null),
		'Original ctype_alnum returns true for a null value.');
}

$this->isTrue(emul_ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_alnum fails to validate [0-9a-z].');
$this->isTrue(emul_ctype_alnum('5686541641'),
	'Emulated ctype_alnum fails to validate random numbers.');
$this->isTrue(emul_ctype_alnum('5A1C9B3F'),
	'Emulated ctype_alnum fails to validate random hexadecimal numbers.');
$this->isTrue(emul_ctype_alnum('fjsdiopfhsiofnuios'),
	'Emulated ctype_alnum fails to validate random letters.');
$this->isTrue(emul_ctype_alnum('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_alnum fails to validate random uppercase letters.');

$this->isFalse(emul_ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_alnum returns true for [0-9a-z?].');
$this->isFalse(emul_ctype_alnum('1.5'),
	'Emulated ctype_alnum returns true for a float number.');
$this->isFalse(emul_ctype_alnum('?*#'),
	'Emulated ctype_alnum returns true for punctuation.');
$this->isFalse(emul_ctype_alnum("\r\n\t"),
	'Emulated ctype_alnum returns true for control characters.');
$this->isFalse(emul_ctype_alnum(' '),
	'Emulated ctype_alnum returns true for a space.');
$this->isFalse(emul_ctype_alnum(''),
	'Emulated ctype_alnum returns true for the empty string.');
$this->isFalse(emul_ctype_alnum(null),
	'Emulated ctype_alnum returns true for a null value.');
