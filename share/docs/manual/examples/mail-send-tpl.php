<?php

$oSendMail = new mySendMail;
$oSendMail->loadTemplate('emails/mytemplate', array(
	'myvar' => $aEvent['post']['myvar'],
	'another' => $sSomeString,
));
$oSendMail->addAddress('someuser@example.org');
$oSendMail->send();
