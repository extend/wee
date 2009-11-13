<?php

// Retrieve the number of rows in a table
$iCount = weeApp()->db->queryValue('SELECT COUNT(*) FROM example_table');
