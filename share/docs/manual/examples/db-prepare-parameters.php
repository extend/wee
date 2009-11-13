<?php

// $oResults is the same result set in the following code snippets.

// Calling weeDatabase::query directly.
$oResults = $oDb->query('SELECT COUNT(*) FROM fails WHERE type = :type', array('type' => 'epic'));

// Using an intermediate prepared statement.
$oStatement = $oDb->prepare('SELECT COUNT(*) FROM fails WHERE type = :type');
$oStatement->bind(array('type' => 'epic'));
$oResults = $oStatement->execute();
