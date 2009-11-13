<?php

// Order by user_email and fetch all users
$oSet->orderBy('user_email');
$oResults = $oSet->fetchAll();

// This is equivalent to this
$oSet->orderBy(array('user_email' => 'ASC'));
$oResults = $oSet->fetchAll();

// And you can also chain the calls
$oResults = $oSet->orderBy('user_email')->fetchAll();

// Order by user_email in reverse order
$oSet->orderBy(array('user_email' => 'DESC'));

// Order by user_gender and then by user_email in reverse order
$oSet->orderBy(array('user_gender', 'user_email' => 'DESC'));

// This is equivalent to this
$oSet->orderBy(array('user_gender' => 'ASC', 'user_email' => 'DESC'));
