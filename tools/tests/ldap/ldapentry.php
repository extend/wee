<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$sDN = 'ou=customers,dc=example,dc=com';
	$oResult = $o->search($sDN, 'cn=*'); // customers & countries

	$oEntry = $oResult->fetch(); //cn=Luke Skywalker,ou=customers,dc=example,dc=com

	$aDN = $oEntry->getExplodedDN(0);
	$this->isEqual(4, $aDN['count'],
		_WT('weeLDAPEntry::getExplodedDN did not split the DN.'));

	$this->isEqual('cn=Luke Skywalker,ou=customers,dc=example,dc=com', $oEntry->getDN(),
		_WT('weeLDAPEntry::getDN did not get the expected DN.'));

	$aEntry['telephoneNumber'][0] = "5555-6666";
	$aEntry['telephoneNumber'][1] = "7777-1235";
	$aEntry['telephoneNumber'][2] = "7777-12135";

	$as['telephoneNumber'][0] = "5555-1234";

	$oEntry->save(); //TODO:Attributes are identical before and after saving, the server is updated but not the object => $rEntry?  re-search?

	$this->isNotNull($oEntry['telephoneNumber'], _WT('This attribute should exists.'));

	unset($oEntry['telephoneNumber']);

	$this->isFalse($oEntry['telephoneNumber'], _WT('This attribute should not exists.'));

} catch (LDAPException $e) {
	$this->fail(sprintf(_WT('weeLDAP should not throw an LDAPException : %s.'), $o->getLastError()));
}
