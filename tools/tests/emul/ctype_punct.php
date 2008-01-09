<?php

require_once('init.php.inc');

if (function_exists('ctype_punct'))
{
	$this->isTrue(ctype_punct('?*#'),
		'Original ctype_punct fails to validate punctuation.');

	$this->isFalse(ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn'),
		'Original ctype_punct returns true for [0-9a-z].');
	$this->isFalse(ctype_punct('5686541641'),
		'Original ctype_punct returns true for random numbers.');
	$this->isFalse(ctype_punct('5A1C9B3F'),
		'Original ctype_punct returns true for random hexadecimal numbers.');
	$this->isFalse(ctype_punct('fjsdiopfhsiofnuios'),
		'Original ctype_punct returns true for random letters.');
	$this->isFalse(ctype_punct('FELMNFKLFDSNFSKLFNSDL'),
		'Original ctype_punct returns true for random uppercase letters.');
	$this->isFalse(ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn?'),
		'Original ctype_punct returns true for [0-9a-z?].');
	$this->isFalse(ctype_punct('1.5'),
		'Original ctype_punct returns true for a float number.');
	$this->isFalse(ctype_punct("\r\n\t"),
		'Original ctype_punct returns true for control characters.');
	$this->isFalse(ctype_punct(' '),
		'Original ctype_punct returns true for a space.');
	$this->isFalse(ctype_punct(''),
		'Original ctype_punct returns true for the empty string.');
	$this->isFalse(ctype_punct(null),
		'Original ctype_punct returns true for a null value.');
}

$this->isTrue(emul_ctype_punct('?*#'),
	'Emulated ctype_punct fails to validate punctuation.');

$this->isFalse(emul_ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn'),
	'Emulated ctype_punct returns true for [0-9a-z].');
$this->isFalse(emul_ctype_punct('5686541641'),
	'Emulated ctype_punct returns true for random numbers.');
$this->isFalse(emul_ctype_punct('5A1C9B3F'),
	'Emulated ctype_punct returns true for random hexadecimal numbers.');
$this->isFalse(emul_ctype_punct('fjsdiopfhsiofnuios'),
	'Emulated ctype_punct returns true for random letters.');
$this->isFalse(emul_ctype_punct('FELMNFKLFDSNFSKLFNSDL'),
	'Emulated ctype_punct returns true for random uppercase letters.');
$this->isFalse(emul_ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn?'),
	'Emulated ctype_punct returns true for [0-9a-z?].');
$this->isFalse(emul_ctype_punct('1.5'),
	'Emulated ctype_punct returns true for a float number.');
$this->isFalse(emul_ctype_punct("\r\n\t"),
	'Emulated ctype_punct returns true for control characters.');
$this->isFalse(emul_ctype_punct(' '),
	'Emulated ctype_punct returns true for a space.');
$this->isFalse(emul_ctype_punct(''),
	'Emulated ctype_punct returns true for the empty string.');
$this->isFalse(emul_ctype_punct(null),
	'Emulated ctype_punct returns true for a null value.');
