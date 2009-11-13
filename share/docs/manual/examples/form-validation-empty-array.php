<?php

try {
	$oForm->validate(array()); // This will fail
} catch (FormValidationException $e) {
	echo $e->toString();
}
