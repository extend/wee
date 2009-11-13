<?php

// Using our previous search
$iCount = $oSet->searchCount(array(
	'user_gender'    => array('=', 'F'),
	'user_birthdate' => array('>=', '1991-01-01'),
));

echo $iCount; // will echo 77
