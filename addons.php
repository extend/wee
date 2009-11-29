#!/usr/bin/php
<?php

// If you have overriden configuration settings in index.php,
// like paths, you might want to override them here as well

// Address to the addons repository. Don't forget the last slash.

define('WEE_ADDONS_REPOSITORY', 'http://addons.extend.ws/');

// Load Web:Extend

define('DEBUG', 1);
define('ALLOW_INCLUSION', 1);
require('wee/wee.php');

// Abort if we're not in CLI

if (!defined('WEE_CLI')) {
	echo _WT('This file is a CLI-only tool and cannot be executed using a browser.');
	exit(1);
}

// Addon manager routines

/**
	Print the addon's ABOUT file, where can be found the license used, authors and more.
	Then print the last known version of the package.
*/

function wee_addons_about($sAddon)
{
	readfile(WEE_ADDONS_REPOSITORY . WEE_VERSION . '/' . $sAddon . '/ABOUT');

	$sVersion = file_get_contents(WEE_ADDONS_REPOSITORY . WEE_VERSION . '/' . $sAddon . '/VERSION');
	echo 'Last known version: ' . $sVersion . "\n";
}

/**
	Build the requested addon.
*/

function wee_addons_build($sAddon, $bForce)
{
	// Obtain the files list

	$sFiles = str_replace("\n", ' ', file_get_contents(ROOT_PATH . 'app/addons/' . $sAddon));
	$sResultFile = ROOT_PATH . 'app/addons/' . $sAddon . '-build.tar.gz';
	$sRecursive = $bForce ? '--recursion' : '--no-recursion';

	// Make the addon's package and store it in app/tmp/[addon name]-build.tar.gz

	exec('tar --exclude-vcs ' . $sRecursive . ' -czf ' . $sResultFile . ' ' . $sFiles);

	// Print path to built file

	echo 'Built package in ' . $sResultFile . "\n";
}

/**
	Install the requested addon.
*/

function wee_addons_install($sAddon, $bForce)
{
	// Refuse to install already existing packages

	if (file_exists(ROOT_PATH . 'app/addons/' . $sAddon)) {
		fwrite(STDERR, 'The addon ' . $sAddon . " is already installed. Please remove it first with the -r option.\n");
		exit(1);
	}

	// First obtain the status of the package, we're expecting 'ok' there
	// otherwise refuse to install unless it's forced through -f

	$sStatus = file_get_contents(WEE_ADDONS_REPOSITORY . WEE_VERSION . '/' . $sAddon . '/STATUS');

	if ($sStatus != 'ok' && !$bForce) {
		fwrite(STDERR, 'The addon ' . $sAddon . ' is marked as unsafe (reason: ' . $sStatus . ").\nUse the -f option to force the installation.\n");
		exit(1);
	}

	// Check if all dependencies are satisfied, otherwise install them

	$aDepends = explode("\n", file_get_contents(WEE_ADDONS_REPOSITORY . WEE_VERSION . '/' . $sAddon . '/DEPENDENCIES'));
	foreach ($aDepends as $sDependency) {
		if (!file_exists(ROOT_PATH . 'app/addons/' . $sDependency))
			wee_addons_install($sDependency, $bForce);
	}

	// Download the file

	$sVersion = file_get_contents(WEE_ADDONS_REPOSITORY . WEE_VERSION . '/' . $sAddon . '/VERSION');
	$sFilename = $sAddon . '-' . $sVersion . '.tar.gz';

	file_put_contents(ROOT_PATH . 'app/tmp/' . $sFilename,
		file_get_contents(WEE_ADDONS_REPOSITORY . WEE_VERSION . '/' . $sAddon . '/' . $sFilename)
	);

	// Save the files structure of the package for later removal

	exec('tar tzf ' . ROOT_PATH . 'app/tmp/' . $sFilename . ' > ' . ROOT_PATH . 'app/addons/' . $sAddon);

	// Extract the addon's package

	system('tar xvzf ' . ROOT_PATH . 'app/tmp/' . $sFilename);
}

/**
	Remove the requested addon.
*/

function wee_addons_remove($sAddon)
{
	// Obtain the files list

	$aFiles = explode("\n", file_get_contents(ROOT_PATH . 'app/addons/' . $sAddon));

	// Remove the last empty line, if any

	$sLast = array_pop($aFiles);
	if (!empty($sLast))
		$aFiles[] = $sLast;

	// Remove all files

	$aDirs = array();

	foreach ($aFiles as $sFile) {
		if (is_dir($sFile))
			$aDirs[] = $sFile;
		else {
			echo 'Removing ' . $sFile . "\n";
			unlink($sFile);
		}
	}

	// Remove all empty directories

	rsort($aDirs);
	foreach ($aDirs as $sFile) {
		if (@rmdir($sFile))
			echo 'Remove directory ' . $sFile . "\n";
		else
			echo 'Failed to remove directory ' . $sFile . "\n";
	}

	// Remove the file from app/addons

	unlink(ROOT_PATH . 'app/addons/' . $sAddon);
}

/**
	Update all the packages to their latest version.
*/

function wee_addons_update()
{
	echo "This feature is not available at this time.\nPlease use the -r and -i manually.\n";
	exit(1);
}

// Route to the required command

$aOptions = getopt('hva:i:r:ufb:p:');

if (isset($aOptions['a']))
	wee_addons_about($aOptions['a']);
elseif (isset($aOptions['i']))
	wee_addons_install($aOptions['i'], isset($aOptions['f']));
elseif (isset($aOptions['r']))
	wee_addons_remove($aOptions['r']);
elseif (isset($aOptions['u']))
	wee_addons_update();
elseif (isset($aOptions['b']))
	wee_addons_build($aOptions['b'], isset($aOptions['f']));
elseif (isset($aOptions['p'])) {
	echo "This feature is not available at this time.\n";
	exit(1);
} elseif (isset($aOptions['v'])) {
	// Print version then quit
	echo 'Addons manager for Web:Extend ' . WEE_VERSION . "\n";
} else {
	// Print usage then quit

	echo <<<EOF
Usage: addons.php [options] [addon]
       addons.php -p filename

Options include:
    -h    print this help message and quit
    -v    print the version and quit
    -a    print information about the addon
    -i    install or update a single addon
    -f    force the installation of an unsafe addon and more
    -r    remove an installed addon; fails if the addon is enabled
    -u    update all the installed addons automatically; use with caution
    -b    build a package of the specified addon; -f to force directory recursion
    -p    push the package to the addons repository (see documentation)

EOF;

	// But quit with an error code in case this wasn't the expected result
	if ($argc != 1 && !isset($aOptions['h']))
		exit(1);
}
