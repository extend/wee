<?php

// The underlying database is PostgreSQL.
$sName = $oDb->meta()->schema('some_schema')->table('some_table')->quotedName();
// $sName = '"some_schema"."some_table"';
