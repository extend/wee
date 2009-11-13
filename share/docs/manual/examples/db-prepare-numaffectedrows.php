<?php

$oStatement = $oDb->prepare("DELETE FROM users WHERE approved = 0");
$oStatement->execute();

printf("%d users were deleted.", $oStatement->numAffectedRows());
