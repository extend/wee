<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('mini');
$oForm->fillErrors(array('textbox' => 'Bad'));
$oForm->fillErrors(array('textbox' => 'Good'));

$this->isMatching('/Good/', $oForm->toString(),
	_WT("The value given to textbox isn't in the generated form."));
