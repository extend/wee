<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$sDN = 'dc=example,dc=com';
	$oResult = $o->search($sDN, 'ou=*', false);

	$oEntry = $oResult->fetch();
	$this->isEqual($oEntry->getDN(), 'ou=customers,dc=example,dc=com',
		_WT('Failed to get the expected entry.'));

	$this->isEqual(2, count($oResult),
		_WT('Bad number of entries.'));

	$aEntries = $oResult->fetchAll();
	$this->isEqual(2, count($aEntries), // customers & countries
		_WT('Bad number of entries.'));

	$oResult->sort('ou');
	$aSorted = array(
		0 => 'ou=countries,dc=example,dc=com',
		1 => 'ou=customers,dc=example,dc=com',
		);

	foreach ($oResult as $key => $entry)
		$aResult[$key] = $entry->getDN();

	$this->isEqual($aSorted, $aResult,
		_WT('The entries were not sorted.'));
} catch (LDAPException $e) {
	$this->fail(_WT('weeLDAP should not throw an LDAPException.'));
}