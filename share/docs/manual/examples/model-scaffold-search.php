<?php

// Retrieve all users that accepted to receive the newsletter
$oResults = $oSet->search(array('user_want_newsletter' => 'IS TRUE'));

// Retrieve the 10 first female users born in or after 1991
$oResults = $oSet->search(array(
	'user_gender'    => array('=', 'F'),
	'user_birthdate' => array('>=', '1991-01-01'),
), 0, 10);
