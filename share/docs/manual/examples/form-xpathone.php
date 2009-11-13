<?php

// Retrieve the widget named user_name
$oNode = $oForm->xpath('widget/name[text()="user_name"]/..');

// Test if a widget exists
try {
	$oNode = $oForm->xpath('widget/name[text()="user_email"]/..');
	// It exists, do something with it
} catch (UnexpectedValueException $e) {
	// It doesn't, do something else
}

// You can also access other things, like HTML elements
$oNode = $oForm->xpath('table[@id="price"]');
$oNode->addAttribute('class', 'high');
