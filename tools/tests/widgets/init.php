<?php

if (isset($_SERVER['argc']))
	return null;

// Initialization

define('ALLOW_INCLUSION',	1);
define('DEBUG',				1);

define('FORM_PATH',	'./form/');
define('ROOT_PATH',	'../../../');
define('TPL_PATH',	'./tpl/');

require(ROOT_PATH . 'wee/wee.php');

$Output = weeXHTMLOutput::instance();

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
		if ($oForm->hasErrors($_POST))
			$oTpl->set('errors', $oForm->getErrors());
	}

	return $oTpl;
}

return null;

?>
