<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$sDN = 'dc=example,dc=com';
	$oResult = $o->search($sDN, 'ou=*');

	$oEntry = $oResult->fetch();
	$this->isEqual($oEntry->getDN(), 'ou=customers,dc=example,dc=com',
		_WT('weeLDAPResult::fetch did not get the expected entry.'));

	$this->isEqual(2, $oResult->numResults(),
		_WT('weeLDAPResult::numResults did not get the expected number of entries.'));

	$aEntries = $oResult->fetchAll();
	$this->isEqual(2, $aEntries['count'], // customers & countries
		_WT('weeLDAPResult::fetchAll did not get the expected number of entries.'));

	$oResult->sort('ou');
	$aSorted = array(
		0 => 'ou=countries,dc=example,dc=com',
		1 => 'ou=customers,dc=example,dc=com',
		);

	foreach ($oResult as $key => $entry)
		$aResult[$key] = $entry->getDN();

	$this->isEqual($aSorted, $aResult,
		_WT('weeLDAPResult::sort did not sort the entries.'));
} catch (LDAPException $e) {
	$this->fail(_WT('weeLDAP should not throw an LDAPException.'));
}