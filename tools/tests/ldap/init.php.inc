<?php

try {
	$o = new weeLDAP(array(
		'host'		=> '127.0.0.1',
		'port'		=> 389,
		'rdn'		=> 'cn=admin,dc=example,dc=com',
		'password'	=> 'wee',
	));
} catch (Exception $e) {
	$this->skip();
}

try {
  $o->delete('cn=Luke Skywalker, ou=customers, dc=example, dc=com');
  $o->delete('cn=Anakin Skywalker, ou=customers, dc=example, dc=com');
  $o->delete('ou=customers, dc=example, dc=com');
  $o->delete('l=Paris, c=FR, ou=countries, dc=example, dc=com');
  $o->delete('l=Marseille, c=FR, ou=countries, dc=example, dc=com');
  $o->delete('c=FR, ou=countries, dc=example, dc=com');
  $o->delete('l=Miami, c=US, ou=countries, dc=example, dc=com');
  $o->delete('l=Washington, c=US, ou=countries, dc=example, dc=com');
  $o->delete('c=US, ou=countries, dc=example, dc=com');
  $o->delete('ou=countries, dc=example, dc=com');
} catch (ErrorException $e) {}

try {
  $o->insert('ou=customers, dc=example, dc=com', array(
    'ou' => 'customers',
    'objectClass' => 'organizationalUnit',
  ));

  $o->insert('cn=Luke Skywalker, ou=customers, dc=example, dc=com', array(
    'objectClass'		=> 'person',
    'sn' 				=> 'Skywalker',
    'telephonenumber' 	=> '5555-1234',
    'description'		=> array('The Grand Master of the New Jedi Order.','other description'),
    'cn' 				=> 'Luke Skywalker',
    'userPassword'		=> 'Luke Skywalker',
  ));

  $o->insert('cn=Anakin Skywalker, ou=customers, dc=example, dc=com', array(
    'objectClass'		=> 'person',
    'sn' 				=> 'Skywalker',
    'telephonenumber' 	=> '5555-1234',
    'description'		=> 'Master of the New Jedi Order.',
    'cn' 				=> 'Anakin Skywalker',
    'userPassword'		=> 'Anakin Skywalker',
  ));

  $o->insert('ou=countries, dc=example, dc=com', array(
    'ou' => 'countries',
    'objectClass' => 'organizationalUnit',
  ));

  $o->insert('c=FR, ou=countries, dc=example, dc=com', array(
    'c' => 'FR',
    'objectClass' => 'country'
  ));

  $o->insert('l=Paris, c=FR, ou=countries, dc=example, dc=com', array(
    'l' => 'Paris',
    'objectClass' => 'locality'
  ));

  $o->insert('l=Marseille, c=FR, ou=countries, dc=example, dc=com', array(
    'l' => 'Marseille',
    'objectClass' => 'locality'
  ));

  $o->insert('c=US, ou=countries, dc=example, dc=com', array(
    'c' => 'US',
    'objectClass' => 'country'
  ));

  $o->insert('l=Miami, c=US, ou=countries, dc=example, dc=com', array(
    'l' 				=> 'Miami',
    'objectClass'		=> 'locality',
  ));

  $o->insert('l=Washington,c=US,ou=countries,dc=example,dc=com', array(
    'l' 				=> 'Washington',
    'objectClass'		=> 'locality',
  ));

} catch (ErrorException $e) {
  $this->skip();
}
