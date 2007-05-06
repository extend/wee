<?php

if (isset($_SERVER['argc']))
	return null;

require('init.php');

$oForm	= new weeForm('selectable');
$oTpl	= new weeTemplate('selectable', array(
	'form'			=> $oForm,
	'is_submitted'	=> !empty($_POST),
));

if (!empty($_POST))
{
	if ($oForm->hasErrors($_POST))
		$oTpl->set('errors', $oForm->getErrors());
	else
	{
	}
}

echo $oTpl;

?>
