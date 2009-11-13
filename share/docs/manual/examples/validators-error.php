<?php

$o = new weeDateValidator(array('invalid_error' => 'What you gave me was not a date!'));
$o->setValue('fail');

if ($o->hasError())
	echo $o->getError(); // What you gave me was not a date!
