<?php

$oResults = myUsersSet::instance()->setDb($oBackupDatabase)->fetchAll();
foreach ($oResults as $oUser)
	$oUser->save();
