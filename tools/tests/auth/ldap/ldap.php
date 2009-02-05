<?php

function_exists('ldap_connect') or $this->skip();

if (!defined('MAGIC_STRING'))
	define('MAGIC_STRING', 'This is a magic string used to salt hash.');

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$o = new weeLDAP(array(
		'host'	=> '127.0.0.1',
		'port'	=> 389,
		'rdn'	=> 'cn=admin,dc=example,dc=com',
		'password'	=> 'wee',
		));

} catch (LDAPException $e) {
	$this->fail('Should not throw an LDAPException.');
}

try {
	$oAuth = new weeAuthLDAP(array(
		'ldap' => $o,
		'base_dn' => 'dc=example, dc=com',
		'hash_treatment' => 'md5',
	));

	$oEntry = $oAuth->authenticate(array(
		'identifier' => 'Luke Skywalker',
		'password' => 'Luke Skywalker',
	));

	$oEntry = $oAuth->authenticateHash(array(
		'identifier' => 'Luke Skywalker',
		'password' => $oAuth->hash('Luke Skywalker'),
	));

} catch (AuthenticationException $e) {
	$this->fail('Should not throw an AuthenticationException .');
}
