<?php

// This could be data sent by a form, for example
$aTest = array(
	'identifier'	=> 'Luke Skywalker',
	'password'		=> 'mypassword42',
);

// Create the authentication driver
$oAuth = new weeAuthLDAP(array(
	'ldap'		=> $oLDAP,
	'base_dn'	=> 'ou=people, dc=example, dc=com',
));

try {
	$oEntry = $oAuth->authenticate($aTest);
	// Authentication succeeded
} catch (AuthenticationException $e) {
	// Authentication failed
}
