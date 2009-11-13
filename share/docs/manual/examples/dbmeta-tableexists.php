<?php

// Check if the table exists in the database
$bTableExists = $oDb->meta()->tableExists('some_table');

// Check if the table exists in a given schema
$bTableExists = $oDb->meta()->schema('some_schema')->tableExists('some_table');
