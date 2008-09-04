<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$aPost = array(
	'hidden'	=> 42,
	'textbox'	=> 'error',
);

$oForm = new weeForm('mini', 'update');
$aFilteredPost = $oForm->filter($aPost);
$this->isEqual($aPost, $aFilteredPost, 'Valid data which should not have been filtered is missing.');
$this->isEqual($aPost['hidden'], $aFilteredPost['hidden'], 'Value for hidden changed.');
$this->isEqual($aPost['textbox'], $aFilteredPost['textbox'], 'Value for textbox changed.');

$oForm = new weeForm('mini');
$aFilteredPost = $oForm->filter($aPost);
$this->isNotEqual($aPost, $aFilteredPost, 'Data should have been filtered.');
$this->isTrue(!isset($aFilteredPost['hidden']), 'Value for hidden found in filtered data.');
$this->isEqual($aPost['textbox'], $aFilteredPost['textbox'], 'Value for textbox changed.');
