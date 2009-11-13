<?php

// List ou=people, dc=example, dc= com by cn
$oResults = $oLDAP->search('ou=people, dc=example, dc= com', 'cn=*', false);

// Iterate over the entries
foreach ($oResults as $oEntry)
	echo $oEntry->getDN() . "\n";
