<?php

// You can define a different WEE_CONF_FILE here
// define('WEE_CONF_FILE', 'app/conf/wee.cnf');
// Defaults to app/conf/wee.cnf

define('ALLOW_INCLUSION', 1);
require('wee/wee.php');

weeApplication::instance();
weeApp()->main();

?>
