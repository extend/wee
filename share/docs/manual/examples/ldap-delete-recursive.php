<?php

// Deleting the sub-entries
$oLDAP->delete('cn=Luke Skywalker, ou=people, dc=example, dc=com');
$oLDAP->delete('cn=chewbacca, ou=people, dc=example, dc=com');

// Deleting the entry
$oLDAP->delete('ou=people, dc=example, dc=com');
