<?php

$o = new weeEmailValidator;
$o->setValue($sEmail);

if ($o->hasError())
	echo $o->getError(); // invalid
else
	doSomething($sEmail); // valid
