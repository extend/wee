<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$sDN = 'cn=Anakin Skywalker, ou=customers, dc=example, dc=com';
	$sAttr = 'objectClass';
	$sValue = 'person';
	$this->isTrue($o->compare($sDN, $sAttr, $sValue),
		sprintf(_WT('weeLDAP::compare should return true because the "%s" attribute of the DN "%s" is "%s".'), $sAttr, $sDN, $sValue));

	$sDN = 'dc=example, dc=com';
	$iResult = $o->ls($sDN, 'ou=*')->numResults();
	$this->isEqual(2, $iResult,
		sprintf(_WT('weeLDAP::ls did not get the expected result in the DN : "%s".'), $sDN));

	$o->update('cn=Anakin Skywalker, ou=customers, dc=example, dc=com', array(
		'telephonenumber' 	=> '5555-6666',
		));

	$oEntry = $o->ls('ou=customers, dc=example, dc=com', 'cn=Anakin Skywalker')->fetch();
	$aAttr = $oEntry->getAttributes();
	
	$this->isEqual('5555-6666', $aAttr['telephoneNumber'][0],
		_WT('weeLDAP::modify should did not modify the telephonenumber attribute.'));

	$sDN = 'ou=countries, dc=example, dc=com';
	$aEntries = $o->read($sDN)->fetchAll();

	$this->isEqual(2, $aEntries[0]['count'], //FR & US
		sprintf(_WT('weeLDAP::read should find entries, in the DN : "%s".'), $sDN));

	$sDN = 'dc=example, dc=com';
	$this->isEqual(2, $o->search($sDN, 'ou=*')->numResults(),
		sprintf(_WT('weeLDAP::search did not find the expected result in the DN : "%s".'), $sDN));

} catch (LDAPException $e) {
	$this->fail(_WT('weeLDAP should not throw an LDAPException.'));
}
