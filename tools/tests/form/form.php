<?php

if (defined('ALLOW_INCLUSION'))
	return false;

// Initialization

define('ALLOW_INCLUSION',	1);
define('DEBUG',				1);

define('FORM_PATH',	'./form/');
define('ROOT_PATH',	'../../../');
define('TPL_PATH',	'./tpl/');

require(ROOT_PATH . 'wee/wee.php');
weeXHTMLOutput::select();

// Generate and display the form

fire(empty($_GET['type']) || !ctype_alnum($_GET['type']));

$oForm	= new weeForm($_GET['type']);
$oTpl	= new weeTemplate('form', array(
	'form'			=> $oForm,
	'is_submitted'	=> !empty($_POST),
));

if (!empty($_POST))
{
	try {
		$oForm->validate($_POST);
	} catch (FormValidationException $e) {
		$oForm->fillErrors($e->getErrors());
		$oTpl->set('errors', $e->toString());
	}
}

echo $oTpl->toString();
