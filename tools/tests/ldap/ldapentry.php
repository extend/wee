<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$sDN = 'ou=customers,dc=example,dc=com';
	$oResult = $o->search($sDN, 'cn=*', true); // customers & countries

	$oEntry = $oResult->fetch(); //cn=Luke Skywalker,ou=customers,dc=example,dc=com

	$this->isEqual('cn=Luke Skywalker,ou=customers,dc=example,dc=com', $oEntry->getDN(),
		_WT('weeLDAPEntry::getDN did not get the expected DN.'));

	$this->isNotNull($oEntry['telephoneNumber'], _WT('This attribute should exists.'));

	unset($oEntry['telephoneNumber']);

	$a['telephoneNumber'][0] = "5555-6666";
	$a['telephoneNumber'][1] = "7777-1235";
	$a['telephoneNumber'][2] = "7777-12135";

	$oEntry['telephoneNumber'] = $a['telephoneNumber'];
	$this->isEqual($oEntry['telephoneNumber'], $a['telephoneNumber'],
		_WT('This expected values for the telephoneNumber attribute of the entry were not found.'));

	$oEntry->save();

	$sDN = 'ou=customers,dc=example,dc=com';
	$oResult = $o->search($sDN, 'cn=*', true);
	$oEntry2 = $oResult->fetch(); //cn=Luke Skywalker,ou=customers,dc=example,dc=com

	$this->isEqual($oEntry['telephoneNumber'], $oEntry2['telephoneNumber'],
		_WT('This entry has not been saved.'));

} catch (LDAPException $e) {
	$this->fail(_WT($e->getErrorMessage()));
}
