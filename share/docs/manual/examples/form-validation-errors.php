<?php

$aData = array(
	'user_id'    => 0,
	'user_name'  => 'me',
	'user_email' => 'respect my privacy!',
	'profile_id' => 'GOD',
);

try {
	$oForm->validate($aData); // This will fail
} catch (FormValidationException $e) {
	echo $e->toString();
}
