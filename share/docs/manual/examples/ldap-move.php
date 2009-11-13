<?php

$sFromDN = 'cn=Luke Skywalker, ou=people, dc=example, dc=com';
$sToDN = 'cn=Luke Skywalker, ou=jedi, dc=example, dc=com';
$oLDAP->move($sFromDN, $sToDN);
