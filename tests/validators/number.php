<?php

$o = new stdClass;

return

// Integer (int)

    weeNumberValidator::test(0)
&&  weeNumberValidator::test(1)
&&  weeNumberValidator::test(-1)
&&  weeNumberValidator::test(111)
&&  weeNumberValidator::test(-111)
&&  weeNumberValidator::test(1.0)
&& !weeNumberValidator::test(1.1)
&&  weeNumberValidator::test(20000000)

// Integer (string)

&&  weeNumberValidator::test('656')
&&  weeNumberValidator::test('20000000000000000')
&&  weeNumberValidator::test('-20000000000000000')
&& !weeNumberValidator::test('1.0')
&& !weeNumberValidator::test('1.1')

// Float (float)

&&  weeNumberValidator::test(0, array('format' => 'float'))
&&  weeNumberValidator::test(1, array('format' => 'float'))
&&  weeNumberValidator::test(1.1, array('format' => 'float'))

// Float (string)

&&  weeNumberValidator::test('0', array('format' => 'float'))
&&  weeNumberValidator::test('0.0', array('format' => 'float'))
&&  weeNumberValidator::test('1', array('format' => 'float'))
&&  weeNumberValidator::test('1.1', array('format' => 'float'))
&& !weeNumberValidator::test('1.1.1', array('format' => 'float'))

// Bad values

&& !weeNumberValidator::test(null)
&& !weeNumberValidator::test('')
&& !weeNumberValidator::test('32f')
&& !weeNumberValidator::test('xxx')
&& !weeNumberValidator::test(true)
&& !weeNumberValidator::test($o)

// Integer min/max

&&  weeNumberValidator::test(0, array('min' => -10))
&& !weeNumberValidator::test(0, array('min' => 10))
&&  weeNumberValidator::test(0, array('max' => 10))
&& !weeNumberValidator::test(0, array('max' => -10))

// Float min/max

&&  weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 0))
&& !weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 2))
&& !weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 0))
&&  weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 2))
&&  weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 1.0))
&& !weeNumberValidator::test(1.1, array('format' => 'float', 'min' => 1.2))
&& !weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 1.0))
&&  weeNumberValidator::test(1.1, array('format' => 'float', 'max' => 1.2))

// Bugs and limitations: these should be valid but aren't yet.

&& !weeNumberValidator::test(20000000000000000)
&& !weeNumberValidator::test(2E+16)
&& !weeNumberValidator::test('2E+16')

;

?>
