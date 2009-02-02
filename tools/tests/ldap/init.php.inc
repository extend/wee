<?php
	$o = new weeLDAP(array(
		'host'	=> '127.0.0.1',
		'port'	=> 389,
		'rdn'	=> 'cn=admin,dc=example,dc=com',
		'password'	=> 'wee',
		));

	$sDN = 'cn=Luke Skywalker, ou=customers, dc=example, dc=com';
	$o->delete($sDN);
	$sDN = 'cn=Anakin Skywalker, ou=customers, dc=example, dc=com';
	$o->delete($sDN);
	$sDN = 'ou=customers, dc=example, dc=com';
	$o->delete($sDN);
	$sDN = 'l=Paris, c=FR, ou=countries, dc=example, dc=com';
	$o->delete($sDN);
	$sDN = 'l=Marseille, c=FR, ou=countries, dc=example, dc=com';
	$o->delete($sDN);
	$sDN = 'c=FR, ou=countries, dc=example, dc=com';
	$o->delete($sDN);
	$sDN = 'l=Miami, c=US, ou=countries, dc=example, dc=com';
	$o->delete($sDN);
	$sDN = 'l=Washington, c=US, ou=countries, dc=example, dc=com';
	$o->delete($sDN);
	$sDN = 'c=US, ou=countries, dc=example, dc=com';
	$o->delete($sDN);
	$sDN = 'ou=countries, dc=example, dc=com';
	$o->delete($sDN);

	$sDN = 'ou=customers, dc=example, dc=com';
	$o->insert($sDN, array(
		'ou' => 'customers',
		'objectClass' => 'organizationalUnit',
		));

	$sDN = 'cn=Luke Skywalker, ou=customers, dc=example, dc=com';
	$o->insert($sDN, array(
		'objectClass'		=> 'person',
		'sn' 				=> 'Skywalker',
		'telephonenumber' 	=> '5555-1234',
		'description'		=> array('The Grand Master of the New Jedi Order.','other description'),
		'cn' 				=> 'Luke Skywalker',
		));

	$sDN = 'cn=Anakin Skywalker, ou=customers, dc=example, dc=com';
	$o->insert($sDN, array(
		'objectClass'		=> 'person',
		'sn' 				=> 'Skywalker',
		'telephonenumber' 	=> '5555-1234',
		'description'		=> 'Master of the New Jedi Order.',
		'cn' 				=> 'Anakin Skywalker',
		));

	$sDN = 'ou=countries, dc=example, dc=com';
	$o->insert($sDN, array(
		'ou' => 'countries',
		'objectClass' => 'organizationalUnit',
		));

	$sDN = 'c=FR, ou=countries, dc=example, dc=com';
	$o->insert($sDN, array(
		'c' => 'FR',
		'objectClass' => 'country'
		));

	$sDN = 'l=Paris, c=FR, ou=countries, dc=example, dc=com';
	$o->insert($sDN, array(
		'l' => 'Paris',
		'objectClass' => 'locality'
		));

	$sDN = 'l=Marseille, c=FR, ou=countries, dc=example, dc=com';
	$o->insert($sDN, array(
		'l' => 'Marseille',
		'objectClass' => 'locality'
		));

	$sDN = 'c=US, ou=countries, dc=example, dc=com';
	$o->insert($sDN, array(
		'c' => 'US',
		'objectClass' => 'country'
		));

	$sDN = 'l=Miami, c=US, ou=countries, dc=example, dc=com';
	$o->insert($sDN, array(
		'l' 				=> 'Miami',
		'objectClass'		=> 'locality',
		));

	$sDN = 'l=Washington,c=US,ou=countries,dc=example,dc=com';
	$o->insert($sDN, array(
		'l' 				=> 'Washington',
		'objectClass'		=> 'locality',
	));
