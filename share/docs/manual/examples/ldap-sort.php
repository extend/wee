<?php

// Listing the organizationalUnits (ou) entries in the domain dc=example, dc=com
$oResults = $oLDAP->search('dc=example, dc=com', 'ou=*', false);

// Sorting the elements by organizationalUnit attribute
$oResults->sort('ou');

foreach ($oResult as $iEntry => $oEntry)
	echo $iEntry . ': ' . $oEntry->getDN();
