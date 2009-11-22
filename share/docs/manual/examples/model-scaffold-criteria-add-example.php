<?php

// Retrieve all the users with an email address from the example.org domain
$oSubset = new myUsersSet(array('user_email' => array('REGEXP', '.+@example\.org')));
$oResults = $oSubset->fetchAll();
