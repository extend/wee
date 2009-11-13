<?php

$oSendMail = new mySendMail;
$oSendMail->Subject	= 'This is the subject';
$oSendMail->Body	= 'Wonderful email from the Web:Extend team!';
$oSendMail->addAddress('someuser@example.org');
$oSendMail->send();
