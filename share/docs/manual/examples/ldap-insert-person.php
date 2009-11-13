<?php

$oLDAP->insert('cn=Luke Skywalker, ou=people, dc=example, dc=com', array(
	'objectClass'	=> 'person',
	'cn'			=> 'Luke Skywalker',
	'sn'			=> 'Skywalker',
	'userpassword'	=> $sCryptedPass,
	'description'	=> array(
		'The Grand Master of the New Jedi Order.',
		'We can have more than one value per attribute.',
	),
));
