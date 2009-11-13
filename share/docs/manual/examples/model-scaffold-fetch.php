<?php

// Simple primary key
$oUser = $oSet->fetch(42); // fetch user with user_id = 42

// Multi-column primary key
$oUser = $oSet->fetch(array('user_id' => 42, 'user_year' => 2009)); // fetch user with user_id = 42 and user_year = 2009
