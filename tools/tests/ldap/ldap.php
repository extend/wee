<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$sDN = 'cn=Anakin Skywalker, ou=customers, dc=example, dc=com';
	$sAttr = 'objectClass';
	$sValue = 'person';
	$this->isTrue($o->isEqual($sDN, $sAttr, $sValue),
		_WT('weeLDAP::isEqual should return true.'));

	$sDN = 'ou=countries, dc=example, dc=com';
	$this->isEqual(2, count($o->search($sDN, 'objectClass=*', false)),
		_WT('weeLDAPResult::count should return 2.'));

	$this->isEqual(7, count($o->search($sDN, 'objectClass=*')),
		_WT('weeLDAPResult::count should return 7.'));

	$o->modify('cn=Anakin Skywalker, ou=customers, dc=example, dc=com', array(
		'telephonenumber' 	=> '5555-6666',
		));

	$oEntry = $o->search('ou=customers, dc=example, dc=com', 'cn=Anakin Skywalker')->fetch();
	$this->isEqual('5555-6666', $oEntry['telephoneNumber'][0],
		_WT('The expected value for the first value of the telephoneNumber attribute was not found.'));

	$sDN = 'ou=countries, dc=example, dc=com';
	$oEntries = $o->search($sDN, 'objectClass=*', false)->fetchAll();

	$this->isEqual(2, count($oEntries), //FR & US
		sprintf(_WT('Bad number of entries, in the DN : "%s".'), $sDN));
} catch (LDAPException $e) {
	$this->fail(_WT('weeLDAP should not throw an LDAPException.'));
}
