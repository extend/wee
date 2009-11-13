<?php

// Insert an user with very few known information
$oSet->insert(array(
	'user_email' => 'test@example.org',
	'profile_id' => 3,
));

// Insert an user for which more information is known
$oSet->insert(array(
	'user_name'   => 'essen',
	'user_email'  => 'essen@example.org',
	'user_gender' => 'M',
	'profile_id'  => 1,
	'project_id'  => 42,
));
