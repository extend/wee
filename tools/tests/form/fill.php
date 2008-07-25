<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('mini');
$oForm->fill(array('textbox' => 'Bad'));
$oForm->fill(array('textbox' => 'Good'));

$this->isMatching('/value="Good"/', $oForm->toString(),
	"The value given to textbox isn't in the generated form.");
