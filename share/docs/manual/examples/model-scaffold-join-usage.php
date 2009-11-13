<?php

$oSet = new myUsersSet;
$oUser = $oSet->fetch(42);

echo 'Your profile is: ', $oUser['profile_label']; // echoes 'Your profile is Administrator'
