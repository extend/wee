<?php

$oResults = myUsersSet::instance()->fetchAll();
foreach ($oResults as $oUser)
	$oUser->setDb($oBackupDatabase)->save();
