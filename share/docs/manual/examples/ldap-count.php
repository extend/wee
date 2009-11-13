<?php

// Listing the organizationalUnits (ou) entries in the domain dc=example, dc=com
$oResults = $oLDAP->search('dc=example, dc=com', 'ou=*', false);

// Display the number of elements found
echo 'Number of groups in example.com: ' . count($oResults);
