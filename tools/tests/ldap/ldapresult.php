<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$sDN = 'dc=example,dc=com';
	$oResult = $o->search($sDN, 'ou=*');
	$i = $oResult->numResults();

	$this->isEqual(2, $i,
		sprintf(_WT('weeLDAPResult::numResults should return 2 got %s instead.'), $i));

	$sDN = 'ou=customers,dc=example,dc=com';
	$this->isEqual($sDN, $oResult->current()->getDN(),
		sprintf(_WT('weeLDAPResult::current should return an weeLDAPEntry object with DN: %s'), $sDN));

	$oResult->fetch();
	$this->isEqual($sDN, $oResult->current()->getDN(),
		sprintf(_WT('weeLDAPResult::fetch : weeLDAPResult::current should return an weeLDAPEntry object with DN: %s. '), $sDN));
	
	$aEntries = $oResult->fetchAll();
	$this->isEqual(2, $aEntries[0]['count'], // customers & countries
		sprintf(_WT('weeLDAPResult::numResults should return 2 elements got %s instead.'), $aEntries[0]['count']));

	$oResult->sort('ou');
	$oResult->fetch();
	$sDN = 'ou=countries,dc=example,dc=com';
	$this->isEqual($sDN, $oResult->current()->getDN(),
		sprintf(_WT('weeLDAPResult::sort : weeLDAPResult::current should return an weeLDAPEntry object with DN: %s.'), $sDN));

	$oResult->next();
	$this->isEqual(1, $oResult->key(),
		sprintf(_WT('weeLDAPResult::key should return 1 got %s instead.'), $oResult->key()));

	$this->isTrue($oResult->valid(),
		sprintf(_WT('weeLDAPResult::valid should return true got %s instead.'), $oResult->valid()));

	$this->isFalse($oResult->offsetExists(-1),
		_WT('weeLDAPResult::offSetExists should not find this offset.'));

} catch (LDAPException $e) {
	$this->fail(_WT('weeLDAP should not throw an LDAPException.'));
}