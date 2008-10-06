<?php

// Path to the configuration file used by the application.

define('WEE_CONF_FILE', 'app/conf/wee.cnf');

// The generated autoload file's path. Please use an absolute path to prevent possible problems.
// Commenting it disable autoload caching and severely reduce the performances of your application.
// @see weeAutoload

define('WEE_AUTOLOAD_FILE', getcwd() . '/app/tmp/autoload.php');

// Uncomment the following line to completely disable caching from your application.
// Use it only if things go wrong with caching. Keep commented otherwise.

// define('NO_CACHE', 1);

// Load Web:Extend

define('ALLOW_INCLUSION', 1);
require('wee/wee.php');

// Start the application

weeApplication::instance()->main();
