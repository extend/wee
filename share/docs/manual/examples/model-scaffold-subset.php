<?php

// Retrieve all users that accepted to receive the newsletter
$oSubset = new mySet(array('user_want_newsletter' => 'IS TRUE'));
$oResults = $oSubset->fetchAll();

// Retrieve the 10 first female users born in or after 1991
$oSubset = new mySet(array(
	'user_gender'		=> array('=', 'F'),
	'user_birthdate'	=> array('>=', '1991-01-01'),
));
$oResults = $oSubset->fetchSubset(0, 10);
