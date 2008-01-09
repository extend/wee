<?php

require_once('init.php.inc');

if (function_exists('ctype_cntrl'))
{
	$this->isTrue(ctype_cntrl("\r\n\t"),
		'Original ctype_cntrl fails to validate control characters.');

	$this->isFalse(ctype_cntrl('fjsdiopfhsiofnuios'),
		'Original ctype_cntrl returns true for random letters.');
	$this->isFalse(ctype_cntrl('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_cntrl returns true for random uppercase letters.');
	$this->isFalse(ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_cntrl returns true for [0-9a-z].');
	$this->isFalse(ctype_cntrl('5686541641'),
		'Original ctype_cntrl returns true for random numbers.');
	$this->isFalse(ctype_cntrl('5A1C9B3F'),
		'Original ctype_cntrl returns true for random hexadecimal numbers.');
	$this->isFalse(ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_cntrl returns true for [0-9a-z?].');
	$this->isFalse(ctype_cntrl('1.5'),
		'Original ctype_cntrl returns true for a float number.');
	$this->isFalse(ctype_cntrl('?*#'),
		'Original ctype_cntrl returns true for punctuation.');
	$this->isFalse(ctype_cntrl(' '),
		'Original ctype_cntrl returns true for a space.');
	$this->isFalse(ctype_cntrl(''),
		'Original ctype_cntrl returns true for the empty string.');
	$this->isFalse(ctype_cntrl(null),
		'Original ctype_cntrl returns true for a null value.');
}

$this->isTrue(emul_ctype_cntrl("\r\n\t"),
	'Emulated ctype_cntrl fails to validate control characters.');

$this->isFalse(emul_ctype_cntrl('fjsdiopfhsiofnuios'),
	'Emulated ctype_cntrl returns true for random letters.');
$this->isFalse(emul_ctype_cntrl('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_cntrl returns true for random uppercase letters.');
$this->isFalse(emul_ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_cntrl returns true for [0-9a-z].');
$this->isFalse(emul_ctype_cntrl('5686541641'),
	'Emulated ctype_cntrl returns true for random numbers.');
$this->isFalse(emul_ctype_cntrl('5A1C9B3F'),
	'Emulated ctype_cntrl returns true for random hexadecimal numbers.');
$this->isFalse(emul_ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_cntrl returns true for [0-9a-z?].');
$this->isFalse(emul_ctype_cntrl('1.5'),
	'Emulated ctype_cntrl returns true for a float number.');
$this->isFalse(emul_ctype_cntrl('?*#'),
	'Emulated ctype_cntrl returns true for punctuation.');
$this->isFalse(emul_ctype_cntrl(' '),
	'Emulated ctype_cntrl returns true for a space.');
$this->isFalse(emul_ctype_cntrl(''),
	'Emulated ctype_cntrl returns true for the empty string.');
$this->isFalse(emul_ctype_cntrl(null),
	'Emulated ctype_cntrl returns true for a null value.');
