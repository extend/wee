<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/auth/ldap/init.php.inc');

	$sDN = 'ou=customers,dc=example,dc=com';
	$oResult = $o->search($sDN, 'cn=*'); // customers & countries

	if ($oResult->valid())
		$oEntry = $oResult->current();

	$a = $oEntry->getAttributes();
	$this->isEqual(5, $a['count'],
		sprintf(_WT('weeLDAPEntry::getAttributes should return 5 attributes got %s instead.'), $a['count']));

	$a = $oEntry->getExplodedDN(0);
	$this->isEqual(4, $a['count'],
		sprintf(_WT('weeLDAPEntry::getExplodedDN should return 4 attributes for %s got %s instead.'), $sDN, $a['count']));

	$sDN = $oEntry->getDN();
	$this->isEqual('cn=Luke Skywalker,ou=customers,dc=example,dc=com', $sDN,
		sprintf(_WT('weeLDAPEntry::getDN should return cn=Luke Skywalker,ou=customers,dc=example,dc=com got %s instead.'), $sDN));

	$a  = array(0 => 'person', 'count' => 1);
	$this->isEqual($a, $oEntry->getAttributeValues(),
		sprintf(_WT('weeLDAPEntry::getAttributeValues did not get the expected values for the attribute %s.'), $oEntry->current()));

	$aEntry['telephoneNumber'][0] = "5555-6666";
	$aEntry['telephoneNumber'][1] = "7777-1235";
	$aEntry['telephoneNumber'][2] = "7777-12135";
	$oEntry->modAdd($oEntry->getDN(), $aEntry);

	//~ $oEntry->offsetGet('telephoneNumber'); //TODO:Attributes are identical befor à after saving, phpldapadmin is updated but not the object => $rEntry?  re-search?

	$as['telephoneNumber'][0] = "5555-1234";
	$oEntry->modModify($oEntry->getDN(), $as);//restore

	$oEntry->rewind();
	$this->isEqual('objectClass', $oEntry->current(),
		_WT('weeLDAPEntry::current should return the current attribute name.'));

	$oEntry->next();
	$oEntry->next();
	$this->isEqual('telephoneNumber', $oEntry->current(),
		_WT('weeLDAPEntry::current should return the current attribute name.'));

	$this->isEqual(2, $oEntry->key(),
		_WT('weeLDAPEntry::key should return the index of the current attribute.'));

	$oEntry->rewind();
	$this->isEqual('objectClass', $oEntry->current(),
		_WT('weeLDAPEntry::current should return the current attribute name.'));

	while($oEntry->current())
		$oEntry->next();

	$this->isFalse($oEntry->valid(),
		_WT('weeLDAPEntry::valid should return false.'));

	$this->isTrue($oEntry->offsetExists('sn'),
		_WT('weeLDAPEntry::offsetExists should return true.'));

	$a  = array(0 => 'person', 'count' => 1);
	$this->isEqual($a , $oEntry->offsetGet('objectClass'),
		_WT('weeLDAPEntry::offsetGet the expected result was not found.'));

	$oEntry->rewind();
	$oEntry->next();
	$oEntry->next(); //moving to telephoneNumber attribute.

	$oEntry->offsetSet('telephoneNumber', array(0 => '911', 1 => '912'));

	$oEntry->save(); //TODO:Attributes are identical befor à after saving, phpldapadmin is updated but not the object => $rEntry?  re-search?

	//~ $a = $oEntry->offsetGet('telephoneNumber');
	//~ $this->isEqual(2 , $a['count'],
		//~ _WT('weeLDAPEntry::offsetGet the expected result was not found.'));

	$oEntry->offsetUnset('telephoneNumber');
	$this->isFalse($oEntry->offsetGet('telephoneNumber'),
		_WT('weeLDAPEntry::offsetGet Should not find the result.'));

} catch (LDAPException $e) {
	$this->fail(sprintf(_WT('weeLDAP should not throw an LDAPException : %s.'), $o->getLastError()));
}
