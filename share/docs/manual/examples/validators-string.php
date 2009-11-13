<?php

$o = new weeStringValidator(array('max' => 42));
$o->setValue($sValue);

if ($o->hasError()) {
	// $sValue has a length greater than 42.
	echo $o->getError();
} else {
	// $sValue is valid, its length is smaller than or equal to 42,
	doSomething($sValue);
}
