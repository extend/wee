<?php

// This prepared statement is a SELECT query.
$oStatement = $oDb->prepare('SELECT 42 AS answer');
$oResults = $oStatement->execute();

// This prepared statement won't return anything.
$oStatement = $oDb->prepare('DELETE FROM answers WHERE value <> 42');
$oStatement->execute();
