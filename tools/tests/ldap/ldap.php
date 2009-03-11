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
		$this->isEqual($o->escape($sValue), $sResult,
			_WT('The strings should be equal.'));

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
