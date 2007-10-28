<?php

class validators_email
{
	public function __toString()
	{
		return 'valid@email.com';
	}
}

$invalid = new stdClass;
$valid = new validators_email;

return

// Valid

    weeEmailValidator::test('test@example.com')
&&  weeEmailValidator::test('test.test@example.com')
&&  weeEmailValidator::test($valid)

// Invalid

&& !weeEmailValidator::test(null)
&& !weeEmailValidator::test('')
&& !weeEmailValidator::test('example')
&& !weeEmailValidator::test('example.com')
&& !weeEmailValidator::test('@example.com')
&& !weeEmailValidator::test('test@example')
&& !weeEmailValidator::test('test@com.example')
&& !weeEmailValidator::test('test@@example.com')
&& !weeEmailValidator::test('test@test@example.com')
&& !weeEmailValidator::test($invalid)

// Other types

&& !weeEmailValidator::test(0)
&& !weeEmailValidator::test(1)
&& !weeEmailValidator::test(-1)
&& !weeEmailValidator::test(111)
&& !weeEmailValidator::test(-111)
&& !weeEmailValidator::test(20000000)
&& !weeEmailValidator::test(1.0)
&& !weeEmailValidator::test(1.1)
&& !weeEmailValidator::test(true)
&& !weeEmailValidator::test(false)
&& !weeStringValidator::test(array(1, 2, 3, 'test', false))

;

?>
