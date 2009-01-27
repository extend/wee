<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$sDN = 'dc=example, dc=com';
	$this->isEqual(1, $o->search($sDN, 'c=FR')->numResults(),
		sprintf(_WT('weeLDAP::search should find : c=US, ou=countries, dc=example, dc=com in the DN : %s.'), $sDN));

	$sDN = 'cn=Anakin Skywalker, ou=customers, dc=example, dc=com';
	$sAttr = 'objectClass';
	$sValue = 'person';
	$this->isTrue($o->compare($sDN, $sAttr, $sValue),
		sprintf(_WT('weeLDAP::compare should return true because the %s attribute of the DN %s is %s.'), $sAttr, $sDN, $sValue));

	$sDN = 'dc=example, dc=com';
	$a = $o->ls($sDN, 'ou=*')->fetchAll();
	$this->isEqual(2, $a['count'], //customers & countries
		sprintf(_WT('weeLDAP::ls should find 2 Organizational Units (ou), in the DN : %s.'), $sDN));

	$oEntries = $o->search($sDN, 'ou=*')->fetchAll();
	$this->isEqual(2, $oEntries['count'], //customers & countries
		sprintf(_WT('weeLDAP::search should find 2 Organizational Units (ou), in the DN : %s.'), $sDN));

	$sDN = 'cn=Anakin Skywalker, ou=customers, dc=example, dc=com';
	$o->modify($sDN, array(
		'telephonenumber' 	=> '5555-6666',
		));

	$sDNTmp = 'ou=customers, dc=example, dc=com';
	$oEntry = $o->ls($sDNTmp, 'cn=Anakin Skywalker')->fetch();

	$aAttr = $oEntry->getAttributes();

	$this->isEqual('5555-6666', $aAttr['telephoneNumber'][0],
		sprintf(_WT('weeLDAP::modify should modify the telephonenumber attribute of %s.'), $sDN));

	$sDN = 'c=US, ou=countries, dc=example, dc=com';

	$aEntries = $o->read($sDN, 'objectClass=*')->fetchAll(); //TODO pk 'locality' 'ou' 'c' throw LDAPException && pk numResults retourn 1
	$this->isEqual(2, $oEntries[0]['count'], //FR & US
		sprintf(_WT('weeLDAP::read should read 2 entries, in the DN : %s.'), $sDN));

} catch (LDAPException $e) {
	$this->fail(_WT('weeLDAP should not throw an LDAPException.'));
}
