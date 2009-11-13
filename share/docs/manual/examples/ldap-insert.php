<?php

$oLDAP->insert('ou=people, dc=example, dc=com', array(
	'objectClass' => 'organizationalUnit',
	'ou' => 'people',
));
