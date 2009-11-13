<?php

$oLDAP->modify('cn=Luke Skywalker, ou=people, dc=example, dc=com', array(
	'sn' => 'New name!',
));
