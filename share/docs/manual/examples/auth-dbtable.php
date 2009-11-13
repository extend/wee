<?php

// This could be data sent by a form, for example
$aTest = array(
	'identifier'	=> 'test@example.org',
	'password'		=> 'mypassword42',
);

// Create the authentication driver
$oAuth = new weeAuthDbTable(array(
	'db'				=> $oDb, // the database driver
	'table'				=> 'users',
	'identifier_field'	=> 'user_email',
	'password_field'	=> 'user_password',
));

try {
	$aResult = $oAuth->authenticate($aTest);
	// Authentication succeeded
} catch (AuthenticationException $e) {
	// Authentication failed
}
