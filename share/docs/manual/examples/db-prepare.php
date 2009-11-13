<?php

$oStatement = weeApp()->db->prepare('INSERT INTO table VALUES(1, 2, 3)');
$oStatement->execute();
