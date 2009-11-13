<?php

$o = new weeTimeValidator(array('max' => '09:42', 'max_error' => 'The value you gave is greater than %max%.'));
$o->setValue('09:43');

if ($o->hasError())
	echo $o->getError(); // The value you gave is greater than 09:42.
