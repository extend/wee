<?php

function_exists('ldap_connect') or $this->skip();

try {
	require(ROOT_PATH . 'tools/tests/ldap/init.php.inc');

	$aEscape = array(
		'\\'											=> '\\\\',
		'+'												=> '\+',
		'++'											=> '\+\+',
		'+++'											=> '\+\+\+',
		'"'												=> '\"',
		'""'											=> '\"\"',
		'"""'											=> '\"\"\"',
		'>'												=> '\>',
		'>>'											=> '\>\>',
		'>>>'											=> '\>\>\>',
		'<'												=> '\<',
		'<<'											=> '\<\<',
		'<<<'											=> '\<\<\<',
		';'												=> '\;',
		';;'											=> '\;\;',
		';;;'											=> '\;\;\;',
		','												=> '\,',
		',,'											=> '\,\,',
		',,,'											=> '\,\,\,',
		'#'												=> '\#',
		'##'											=> '\##',
		'###'											=> '\###',
		' '												=> '\ ',
		'  '											=> '\ \ ',
		'   '											=> '\  \ ',
		'\\\\'											=> '\\\\\\\\',
		'\\ \\'											=> '\\\\ \\\\',
		'\+'											=> '\\\\\+',
		'\++'											=> '\\\\\+\+',
		'\"'											=> '\\\\\"',
		'\""'											=> '\\\\\"\"',
		'\>'											=> '\\\\\>',
		'\>>'											=> '\\\\\>\>',
		'\>\>'											=> '\\\\\>\\\\\>',
		'\<'											=> '\\\\\<',
		'\<<'											=> '\\\\\<\<',
		'\<\<'											=> '\\\\\<\\\\\<',
		'\;'											=> '\\\\\;',
		'\;;'											=> '\\\\\;\;',
		'\;\;'											=> '\\\\\;\\\\\;',
		'\,'											=> '\\\\\,',
		'\,,'											=> '\\\\\,\,',
		'\,\,'											=> '\\\\\,\\\\\,',
		'\#'											=> '\\\\#',
		'\##'											=> '\\\\##',
		'\#\#'											=> '\\\\#\\\\#',
		'\ '											=> '\\\\\ ',
		'\  '											=> '\\\\ \ ',
		'\ \ '											=> '\\\\ \\\\\ ',
		' \ \ '											=> '\ \\\\ \\\\\ ',
		'x '											=> 'x\ ',
		' x'											=> '\ x',
		''												=> '',
		' x '											=> '\ x\ ',
		' space at the beggining'						=> '\ space at the beggining',
		'space at the end '								=> 'space at the end\ ',
		' space at the beggining and end '				=> '\ space at the beggining and end\ ',
		'   three spaces at the beggining'				=> '\   three spaces at the beggining',
		'three spaces at the end   '					=> 'three spaces at the end  \ ',
		'   three spaces at the beggining and end   '	=> '\   three spaces at the beggining and end  \ ',
		'#octothorp at the beggining'					=> '\#octothorp at the beggining',
		'octothorp at the end#'							=> 'octothorp at the end#',
		'#octothorp at the beggining and end#'			=> '\#octothorp at the beggining and end#',
		'multivalued+mail=luky@example.com'				=> 'multivalued\+mail=luky@example.com',
		'multivalued + mail=luky@example.com'			=> 'multivalued \+ mail=luky@example.com',
		' \\+"><;,#'									=> '\ \\\\\+\"\>\<\;\,#',
		'&1~"#\'{([-|`_\\^@)]=}+%^$ا!<>:/;.,?*'		=> '&1~\"#\'{([-|`_\\\\^@)]=}\+%^$ا!\<\>:/\;.\,?*',
		' &1~"#\'{([-|`_\\^@)]=}+%^$ا!<>:/;.,?* '		=> '\ &1~\"#\'{([-|`_\\\\^@)]=}\+%^$ا!\<\>:/\;.\,?*\ ',
	);

	foreach ($aEscape as $sValue => $sResult)
		$this->isEqual($sResult, $o->escape($sValue),
			_WT('The strings should be equal.'));

	$aEscape = array(
		'*'			=> '\2A',
		'**'		=> '\2A\2A',
		'('			=> '\28',
		'(('		=> '\28\28',
		')'			=> '\29',
		'))'		=> '\29\29',
		'\\'		=> '\5C',
		'\\\\'		=> '\5C\5C',
		"\0"		=> '\00',
		'A*'		=> 'A\2A',
		'A('		=> 'A\28',
		'A)'		=> 'A\29',
		'A\\'		=> 'A\5C',
		"A\0"		=> 'A\00',
		'*A'		=> '\2AA',
		'(A'		=> '\28A',
		')A'		=> '\29A',
		'\A'		=> '\5CA',
		"\0A"		=> '\00A',
		'A*(astar)'	=> 'A\2A\28astar\29',
		'(42*42)'	=> '\2842\2A42\29',
		'es\\cape'	=> 'es\5Ccape',
		"es\0cape"	=> 'es\00cape',
		'es*cape'	=> 'es\2Acape',
		'es(cape'	=> 'es\28cape',
		'es)cape'	=> 'es\29cape',
		'es\cape'	=> 'es\5Ccape',
		"*()\\\0"	=> '\2A\28\29\5C\00',
	);

	foreach ($aEscape as $sValue => $sResult)
		$this->isEqual($sResult, $o->escapeFilter($sValue),
			_WT('The strings should be equal.'));

	$sDN = 'cn=Anakin Skywalker, ou=customers, dc=example, dc=com';
	$sAttr = 'objectClass';
	$sValue = 'person';
	$this->isTrue($o->isEqual($sDN, $sAttr, $sValue),
		_WT('weeLDAP::isEqual should return true.'));

	$aEntry = array (
		'ou'			=> array('customers'),
		'objectClass'	=> array('organizationalUnit'),
	);

	$this->isEqual($aEntry, $o->fetch('ou=customers, dc=example, dc=com')->toArray(),
		_WT('The arrays should be equal.'));

	$aEntry = array (
		'objectClass'		=> array('person'),
		'sn'				=> array('Skywalker'),
		'telephoneNumber'	=> array('5555-1234'),
		'description'		=> array('Master of the New Jedi Order.'),
		'cn'				=> array('Anakin Skywalker'),
		'userPassword'		=> array(sha1('Anakin Skywalker')),
	);

	$this->isEqual($aEntry, $o->fetch('cn=Anakin Skywalker, ou=customers, dc=example, dc=com', 'cn=*')->toArray(),
		_WT('The arrays should be equal.'));

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
