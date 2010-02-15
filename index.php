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

/*
	CONFIGURATION ENDS HERE. BOOTSTRAP CODE FOLLOWS.

	You shouldn't need to edit past this point unless you have very specific needs.
*/

// Load Web:Extend

define('ALLOW_INCLUSION', 1);
require('wee/wee.php');
require('wee/weexlib.php');

// Load the configuration and create the default application object.

try {
	$aConfig = weeConfigFile::cachedLoad(WEE_CONF_FILE, WEE_CONF_CACHE);
} catch (FileNotFoundException $e) {
	// The configuration file doesn't exist. Stop here and display a friendly message.

	if (defined('WEE_CLI'))
		echo _WT('The configuration file was not found.') . "\n" .
			_WT('Please consult the documentation for more information.') . "\n";
	else {
		if (defined('DEBUG'))
			FirePHP::getInstance(true)->fb($e);

		header('HTTP/1.0 500 Internal Server Error');
		require(ROOT_PATH . 'res/wee/noconfig.htm');
	}

	exit;
}

weeApplication::setSharedInstance(new weeApplication($aConfig));
unset($aConfig); // Clean-up.

/**
	The shorthand function to get the shared application instance.
	@return The shared weeApplication instance.
	@see weeApplication::sharedInstance
*/

function weeApp() {
	return weeApplication::sharedInstance();
}

// Everything is ready, start the application.

weeApplication::sharedInstance()->main();
