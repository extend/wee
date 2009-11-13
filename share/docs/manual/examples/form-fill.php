<?php

// Filter GET data here...

$aUser = fetchUser($_GET['id']);
$oForm->fill($aUser);

if (!empty($_POST))
	$oForm->fill($_POST);
