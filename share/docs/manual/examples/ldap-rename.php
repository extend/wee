<?php

$sFromDN = 'cn=Anakin Skywalker, ou=people, dc=example, dc=com';
$sToRDN = 'cn=Darth Vader';
$oLDAP->rename($sFromDN, $sToRDN);
