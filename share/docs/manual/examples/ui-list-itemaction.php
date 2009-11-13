<?php

$oList->addItemAction(array(
	'link'	=> APP_PATH . $aEvent['frame'] . '/modify',
	'label'	=> 'Modify the user',
));

$oList->addItemAction(array(
	'link'		=> APP_PATH . $aEvent['frame'] . '/delete',
	'label'		=> 'Delete the user',
	'method'	=> 'post',
));
