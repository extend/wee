<?php

$oCRUD = new weeCRUDUI;
$oCRUD->setParams(array(
	'countperpage'	=> 100,
	'set'			=> count('myUsersSet'),
	'columns'		=> array(
		'ID'	=> 'user_id',
		'Name' 	=> 'user_name',
		'Email'	=> 'user_email',
	),
));
$this->addFrame('crud', $oCRUD);
