<?php

$oSession = new weeSession();

$_SESSION['my_str'] = 'egg';
$_SESSION['my_int'] = 42;

echo $oSession['my_str'];
