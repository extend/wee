<?php

class validators_string
{
	public function __toString()
	{
		return 'valid string?';
	}
}

$o = new stdClass;
$s = new validators_string;

return

// Strings

    weeStringValidator::test(null)
&&  weeStringValidator::test('')
&&  weeStringValidator::test('32')
&&  weeStringValidator::test('xxx')
&&  weeStringValidator::test(str_repeat('x', 100000))

// Other types

&&  weeStringValidator::test(0)
&&  weeStringValidator::test(1)
&&  weeStringValidator::test(-1)
&&  weeStringValidator::test(111)
&&  weeStringValidator::test(-111)
&&  weeStringValidator::test(20000000)
&&  weeStringValidator::test(1.0)
&&  weeStringValidator::test(1.1)
&&  weeStringValidator::test(true)
&&  weeStringValidator::test(false)

// Arrays and classes

&& !weeStringValidator::test(array(1, 2, 3, 'test', false))
&& !weeStringValidator::test($o)
&&  weeStringValidator::test($s)

// Length tests

&&  weeStringValidator::test('oeuf', array('len' => 4))
&& !weeStringValidator::test('oeuf', array('len' => 5))
&& !weeStringValidator::test('oeuf', array('len' => 3))

&&  weeStringValidator::test('oeuf', array('min' => 4))
&&  weeStringValidator::test('oeuf', array('min' => 1))
&& !weeStringValidator::test('oeuf', array('min' => 10))

&&  weeStringValidator::test('oeuf', array('max' => 4))
&& !weeStringValidator::test('oeuf', array('max' => 1))
&&  weeStringValidator::test('oeuf', array('max' => 10))

// Bugs and limitations: these should NOT be valid but are.

&&  weeStringValidator::test("string \0 possible hack if this string is used to open file, for example")
&& !weeStringValidator::test('oeuf', array('len' => -1))
&&  weeStringValidator::test('oeuf', array('min' => -1))
&& !weeStringValidator::test('oeuf', array('max' => -1))
&& !weeStringValidator::test('oeuf', array('min' => 6, 'max' => 2))

;

?>
