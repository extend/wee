<?php

// Uncomment the following line to enable DEBUG mode.
// This has the effect to turn on all PHP errors and warnings,
// to provide you with an useful trace when an exception occurs,
// and to disable any caching that might happen in the framework.
// Do NOT enable DEBUG mode on a production environment.

// define('DEBUG', 1);

// Path to the configuration file used by the application.

define('WEE_CONF_FILE', 'app/conf/wee.cnf');

// Cache for the configuration file.
// Commenting it disable configuration caching and moderately reduce the performances of your application.

define('WEE_CONF_CACHE', 'app/tmp/config.php');

// The generated autoload file's path. Please use an absolute path to prevent possible problems.
// Commenting it disable autoload caching and severely reduce the performances of your application.
// @see weeAutoload

define('WEE_AUTOLOAD_CACHE', getcwd() . '/app/tmp/autoload.php');

// Uncomment the following line to completely disable caching from your application.
// Use it only if things go wrong with caching. Keep commented otherwise.

// define('NO_CACHE', 1);

// The following string will be used as salt to enhance protection when using some of the
// session and cookie-related features in the framework. Do NOT use the default value.

// define('MAGIC_STRING', 'This is a magic string used to salt various hash throughout the framework.');

// Load Web:Extend

define('ALLOW_INCLUSION', 1);
require('wee/wee.php');

// Start the application

weeApplication::instance()->main();
