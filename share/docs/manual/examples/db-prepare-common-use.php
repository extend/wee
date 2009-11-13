<?php

$oStatement = $oDb->prepare('UPDATE news SET category = :new WHERE category = :old');

$oStatement->bind(array('old' => 'GAIM', 'new' => 'Pidgin'));
$oStatement->execute();
printf('%d news were moved from "GAIM" to "Pidgin".', $oStatement->numAffectedRows());

// You can also chain these methods if you wish to
$oStatement->bind(array('old' => 'development', 'new' => 'dev'))->execute();
printf('%d news were moved from "development" to "dev".', $oStatement->numAffectedRows());
