<?php

// Initialization

define('ALLOW_INCLUSION',	1);
define('DEBUG',				1);

define('FORM_PATH',	'./form/');
define('ROOT_PATH',	'../../../');
define('TPL_PATH',	'./tpl/');

require(ROOT_PATH . 'wee/wee.php');

weeXHTMLOutput::select();

// Convenience functions

function weeFormTest($sForm)
{
	$oForm	= new weeForm($sForm);
	$oTpl	= new weeTemplate('form', array(
		'form'			=> $oForm,
		'is_submitted'	=> !empty($_POST),
	));

	if (!empty($_POST))
	{
		try {
			$oForm->validate($_POST);
		} catch (FormValidationException $e) {
			$oTpl->set('errors', $e->toString());
		}
	}

	return $oTpl;
}
