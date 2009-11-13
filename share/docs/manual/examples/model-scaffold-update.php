<?php

$oUser = $oSet->fetch(42);
$oUser['user_email'] = 'mynewmail@example.org';
$oUser->update();
