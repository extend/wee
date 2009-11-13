<?php

// Our data from previous examples
$aTest = array(
	'identifier'	=> 'test@example.org',
	'password'		=> 'mypassword42',
);

// This is how you can generate a hash
$aTest['password'] = $oAuth->hash($aTest['password']);

try {
	// And this is how you check if it's valid
	$aResult = $oAuth->authenticateHash($aTest);
	// Authentication succeeded
} catch (AuthenticationException $e) {
	// Authentication failed
}
