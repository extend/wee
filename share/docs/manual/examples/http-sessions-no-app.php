<?php

$oSession = new weeSession();

$oSession['my_str'] = 'egg';
$oSession['my_int'] = 42;

echo $oSession['my_str'];
