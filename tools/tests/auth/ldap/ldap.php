<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$o = new weeLDAP(array(
		'host'		=> '127.0.0.1',
		'port'		=> 389,
		'rdn'		=> 'cn=admin,dc=example,dc=com',
		'password'	=> 'wee',
	));
} catch (LDAPException $e) {
	$this->fail('Should not throw an LDAPException.');
}

try {
	$oAuth = new weeAuthLDAP(array(
		'base_dn'	=> 'dc=example, dc=com',
		'host'		=> '127.0.0.1',
	));
	$this->fail('Should throw an InvalidArgumentException, the ldap object is missing.');
} catch (InvalidArgumentException $e) {}

try {
	$oAuth = new weeAuthLDAP(array(
		'ldap' => $o,
		'host' => '127.0.0.1',
	));
	$this->fail('Should throw an InvalidArgumentException, the parameter base_dn is missing.');
} catch (InvalidArgumentException $e) {}

try {
	$oAuth = new weeAuthLDAP(array(
		'ldap'					=> $o,
		'base_dn'				=> 'dc=example, dc=com',
		'host'					=> '127.0.0.1',
		'password_treatment'	=> 'blah',
	));
	$this->fail('Should throw an InvalidArgumentException, the password_treatment parameter is not callable.');
} catch (InvalidArgumentException $e) {}


try {
	$oAuth = new weeAuthLDAP(array(
		'ldap'		=> $o,
		'base_dn'	=> 'dc=example, dc=com',
	));
	$this->fail('Should throw an InvalidArgumentException, the host parameter is missing.');
} catch (InvalidArgumentException $e) {}

try {
	$oAuth = new weeAuthLDAP(array(
		'ldap'		=> $o,
		'base_dn'	=> 'dc=example, dc=com',
		'host'		=> '127.0.0.1',
	));
} catch (InvalidArgumentException $e) {
	$this->fail('Should not throw an InvalidArgumentException.');
}

try {
	$oEntry = $oAuth->authenticateHash(array());
	$this->fail('Should throw an IllegalStateException, there MAGIC_STRING is not defined.');
} catch (IllegalStateException $e) {}

if (!defined('MAGIC_STRING'))
	define('MAGIC_STRING', 'This is a magic string used to salt hash.');

try {
	$oEntry = $oAuth->authenticate(array(
		'identifier'	=> 'unexistant',
		'password'		=> 'idem',
	));
	$this->fail('Should throw an LDAPException, credentials are incorrect.');
} catch (LDAPException $e) {}

try {
	$oEntry = $oAuth->authenticate(array(
		'identifier'	=> '',
		'password'		=> '',
	));
	$this->fail('Should throw an LDAPException, credentials are incorrect.');
} catch (LDAPException $e) {}

try {
	$oEntry = $oAuth->authenticateHash(array(
		'identifier'	=> 'unexistant',
		'password'		=> 'idem',
	));
	$this->fail('Should throw an LDAPException, credentials are incorrect.');
} catch (LDAPException $e) {}

try {
	$oEntry = $oAuth->authenticateHash(array(
		'identifier'	=> '',
		'password'		=> '',
	));
	$this->fail('Should throw an LDAPException, credentials are incorrect.');
} catch (LDAPException $e) {}

try {
	$oEntry = $oAuth->authenticateHash(array(
		'identifier'	=> 'Luke Skywalker',
		'password'		=> $oAuth->hash('bad password'),
	));
	$this->fail('Should throw an AuthenticationException, the password value is incorrect.');
} catch (AuthenticationException $e) {}

try {
	$oEntry = $oAuth->authenticate(array(
		'identifier'	=> 'Luke Skywalker',
		'password'		=> 'Luke Skywalker',
	));
	$this->isTrue(empty($oEntry['userPassword']), _WT('The password has not been removed from the entry.'));

	$oEntry = $oAuth->authenticateHash(array(
		'identifier'	=> 'Luke Skywalker',
		'password'		=> $oAuth->hash('Luke Skywalker'),
	));
	$this->isTrue(empty($oEntry['userPassword']), _WT('The password has not been removed from the entry.'));
} catch (AuthenticationException $e) {
	$this->fail('Should not throw an AuthenticationException.');
}

try {
	$o = new weeLDAP(array(
		'host'		=> '127.0.0.1',
		'port'		=> 389,
		'rdn'		=> 'cn=Luke Skywalker,ou=customers,dc=example,dc=com',
		'password'	=> sha1('Luke Skywalker'),
	));

	$oAuth = new weeAuthLDAP(array(
		'ldap'		=> $o,
		'base_dn'	=> 'dc=example, dc=com',
		'host'		=> '127.0.0.1',
	));

	$oEntry = $oAuth->authenticateHash(array(
		'identifier'	=> 'admin',
		'password'		=> $oAuth->hash('wee'),
	));

	$this->fail('Should throw an UnexpectedValueException, because user Luke Skywalker is not allowed to get admin password.');
} catch (UnexpectedValueException $e) {}
