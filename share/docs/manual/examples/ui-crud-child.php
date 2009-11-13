<?php

if ($aEvent['name'] == 'add' || $aEvent['name'] == 'update') {
	$oForm = $oCRUD->child('form');
	doSomething($oForm);
}
