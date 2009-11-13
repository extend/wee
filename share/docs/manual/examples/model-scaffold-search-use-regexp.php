<?php

$oSet = new myUsersSet;

// Retrieve all the users with an email address from the example.org domain
$oResults = $oSet->search(array('user_email' => array('REGEXP', '.+@example\.org')));
