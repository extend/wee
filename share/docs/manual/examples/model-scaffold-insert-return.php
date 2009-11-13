<?php

$oUser = $oSet->insert(array(
	'user_email' => 'test@example.org',
	'profile_id' => 3,
));

echo $oUser['user_email']; // echoes 'test@example.org'
