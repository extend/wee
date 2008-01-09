<?php

require_once('init.php.inc');

if (function_exists('ctype_lower'))
{
	$this->isTrue(ctype_lower('fjsdiopfhsiofnuios'),
		'Original ctype_lower fails to validate random lowercase letters.');

	$this->isFalse(ctype_lower('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_lower returns true for random uppercase letters.');
	$this->isFalse(ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_lower returns true for [0-9a-z].');
	$this->isFalse(ctype_lower('5686541641'),
		'Original ctype_lower returns true for random numbers.');
	$this->isFalse(ctype_lower('5A1C9B3F'),
		'Original ctype_lower returns true for random hexadecimal numbers.');
	$this->isFalse(ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_lower returns true for [0-9a-z?].');
	$this->isFalse(ctype_lower('1.5'),
		'Original ctype_lower returns true for a float number.');
	$this->isFalse(ctype_lower('?*#'),
		'Original ctype_lower returns true for punctuation.');
	$this->isFalse(ctype_lower("\r\n\t"),
		'Original ctype_lower returns true for control characters.');
	$this->isFalse(ctype_lower(' '),
		'Original ctype_lower returns true for a space.');
	$this->isFalse(ctype_lower(''),
		'Original ctype_lower returns true for the empty string.');
	$this->isFalse(ctype_lower(null),
		'Original ctype_lower returns true for a null value.');
}

$this->isTrue(emul_ctype_lower('fjsdiopfhsiofnuios'),
	'Emulated ctype_lower fails to validate random lowercase letters.');

$this->isFalse(emul_ctype_lower('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_lower returns true for random uppercase letters.');
$this->isFalse(emul_ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_lower returns true for [0-9a-z].');
$this->isFalse(emul_ctype_lower('5686541641'),
	'Emulated ctype_lower returns true for random numbers.');
$this->isFalse(emul_ctype_lower('5A1C9B3F'),
	'Emulated ctype_lower returns true for random hexadecimal numbers.');
$this->isFalse(emul_ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_lower returns true for [0-9a-z?].');
$this->isFalse(emul_ctype_lower('1.5'),
	'Emulated ctype_lower returns true for a float number.');
$this->isFalse(emul_ctype_lower('?*#'),
	'Emulated ctype_lower returns true for punctuation.');
$this->isFalse(emul_ctype_lower("\r\n\t"),
	'Emulated ctype_lower returns true for control characters.');
$this->isFalse(emul_ctype_lower(' '),
	'Emulated ctype_lower returns true for a space.');
$this->isFalse(emul_ctype_lower(''),
	'Emulated ctype_lower returns true for the empty string.');
$this->isFalse(emul_ctype_lower(null),
	'Emulated ctype_lower returns true for a null value.');
