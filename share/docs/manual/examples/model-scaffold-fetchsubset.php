<?php

// Fetch 10 rows from offset 42
$oResults = $oSet->fetchSubset(42, 10);

// Fetch all the rows from the table
$oResults = $oSet->fetchSubset();

// You can then iterate over the results
foreach ($oResults as $oUser)
	echo $oUser['user_name'] . ',';
