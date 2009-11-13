<?php

$oResults = $oDb->query('SELECT * FROM users LIMIT 100 OFFSET ?', array_value($aEvent['get'], 'from', 0));

$oList = new weeListUI;
$oList->setParams(array(
	'countperpage'	=> 100,
	'total'			=> count($oResults),
	'columns'		=> array(
		'ID'	=> 'user_id',
		'Name'	=> 'user_name',
		'Email'	=> 'user_email',
	),
));
$oList->setList($oResults);
$this->addFrame('list', $oList);
