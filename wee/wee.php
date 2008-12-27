<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ALLOW_INCLUSION')) die;
if (version_compare(phpversion(), '5.0.0', '<')) die;

// Prevent the script from using the default value for MAGIC_STRING

!defined('MAGIC_STRING') or MAGIC_STRING != 'This is a magic string used to salt various hash throughout the framework.'
	or die('The constant MAGIC_STRING defined in your script is using the default value. Please change its value before retrying.');

// Enable/disable error reporting depending on DEBUG

if (defined('DEBUG')) {
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	ini_set('display_errors', 0);
}

// Detect whether we are on Windows

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	define('WEE_ON_WINDOWS', 1);

// Detect whether we are using the CLI

if (PHP_SAPI == 'cli')
	define('WEE_CLI', 1);

// Paths and files extensions

if (!defined('BASE_PATH')) {
	$i = substr_count(substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME'])), '/')
		- (int)(isset($_SERVER['REDIRECT_URL']));

	for ($s = null; $i > 0; $i--)
		$s .= '../';

	define('BASE_PATH', $s);

	unset($i, $s);
}
if (!defined('ROOT_PATH'))	define('ROOT_PATH',	'./');
if (!defined('APP_PATH')) {
	if (ROOT_PATH == './')	define('APP_PATH', BASE_PATH);
	else					define('APP_PATH', BASE_PATH . ROOT_PATH);
}
if (!defined('WEE_PATH'))	define('WEE_PATH', ROOT_PATH . 'wee/');
if (!defined('PHP_EXT'))	define('PHP_EXT',  strrchr(__FILE__, '.'));
if (!defined('CLASS_EXT'))	define('CLASS_EXT',	'.class' . PHP_EXT);

// Define the LC_MESSAGES constant if not defined

if (!defined('LC_MESSAGES'))
	define('LC_MESSAGES', 5);

// Atomize magic quotes

set_magic_quotes_runtime(0);
if (get_magic_quotes_gpc())
{
	// Can't use array_walk_recursive: see http://fr2.php.net/manual/en/function.array-walk-recursive.php#81835
	function mqs(&$a) { foreach ($a as &$m) if (is_array($m)) mqs($m); else $m = stripslashes($m); }

	mqs($_GET);
	mqs($_POST);
	mqs($_COOKIE);
	mqs($_FILES);

	// PHP configuration should reflect the fact that magic quotes have been removed

	ini_set('magic_quotes_gpc',		0);
	ini_set('magic_quotes_sybase',	0);
}

// Translation functions

if (!defined('APP_LOCALE_DOMAIN'))	define('APP_LOCALE_DOMAIN', 'app');
if (!defined('APP_LOCALE_PATH'))	define('APP_LOCALE_PATH', ROOT_PATH . 'app/locale');

if (function_exists('gettext') && !defined('WEE_TRANSLATE')) {
	/**
		Translate the given string in the current locale using the current domain.
		This function do both gettext and ngettext depending on the number of arguments given.

		@overload _T($sText) Translate the given text.
		@overload _T($sText, $sPlural, $iCount) Plural version of text translation.
		@return string The translated text.
	*/

	function _T()
	{
		$aArgs = func_get_args();

		if (count($aArgs) == 1)
			return gettext($aArgs[0]);

		count($aArgs) == 3 or burn('InvalidArgumentException',
			sprintf(_WT('The %s function requires either 1 or 3 arguments.'), '_T'));

		return ngettext($aArgs[0], $aArgs[1], $aArgs[2]);
	}

	/**
		Translate the given string in the current locale using the framework's domain (wee).
		This function do both gettext and ngettext depending on the number of arguments given.
		This function is reserved for internal use.

		@overload _T($sText) Translate the given text.
		@overload _T($sText, $sPlural, $iCount) Plural version of text translation.
		@return string The translated text.
	*/

	function _WT()
	{
		$aArgs = func_get_args();

		if (count($aArgs) == 1)
			return dgettext('wee', $aArgs[0]);

		count($aArgs) == 3 or burn('InvalidArgumentException',
			sprintf(_WT('The %s function requires either 1 or 3 arguments.'), '_WT'));

		return dngettext('wee', $aArgs[0], $aArgs[1], $aArgs[2]);
	}

	// Create the domains and choose the application's domain as default

	bindtextdomain(APP_LOCALE_DOMAIN, APP_LOCALE_PATH);
	bindtextdomain('wee', ROOT_PATH . 'share/locale');
	textdomain(APP_LOCALE_DOMAIN);
} else {
	/**
		Translate the given string in the current locale using the current domain.
		This function do both gettext and ngettext depending on the number of arguments given.

		@overload _T($sText) Translate the given text.
		@overload _T($sText, $sPlural, $iCount) Plural version of text translation.
		@return string The translated text.
	*/

	function _T()
	{
		static $sLocale = null;
		static $oDictionary = null;

		$sCurrentLocale = setlocale(LC_MESSAGES, 0);
		if ($sLocale != $sCurrentLocale) {
			$sLocale = $sCurrentLocale;

			$a = explode('.', $sLocale);
			$oDictionary = new weeGetTextDictionary(APP_LOCALE_PATH . '/' . $a[0] . '/LC_MESSAGES/' . APP_LOCALE_DOMAIN . '.mo');
		}

		$aArgs = func_get_args();

		if (count($aArgs) == 1)
			return $oDictionary->getTranslation($aArgs[0]);

		count($aArgs) == 3 or burn('InvalidArgumentException',
			sprintf(_WT('The %s function requires either 1 or 3 arguments.'), '_T'));

		return $oDictionary->getPluralTranslation($aArgs[0], $aArgs[1], $aArgs[2]);
	}

	/**
		Translate the given string in the current locale using the framework's domain (wee).
		This function do both gettext and ngettext depending on the number of arguments given.
		This function is reserved for internal use.

		@overload _T($sText) Translate the given text.
		@overload _T($sText, $sPlural, $iCount) Plural version of text translation.
		@return string The translated text.
	*/

	function _WT()
	{
		static $sLocale = null;
		static $oDictionary = null;

		$sCurrentLocale = setlocale(LC_MESSAGES, 0);
		if ($sLocale != $sCurrentLocale) {
			$sLocale = $sCurrentLocale;

			$a = explode('.', $sLocale);
			$oDictionary = new weeGetTextDictionary(ROOT_PATH . 'share/locale/' . $a[0] . '/LC_MESSAGES/wee.mo');
		}

		$aArgs = func_get_args();

		if (count($aArgs) == 1)
			return $oDictionary->getTranslation($aArgs[0]);

		count($aArgs) == 3 or burn('InvalidArgumentException',
			sprintf(_WT('The %s function requires either 1 or 3 arguments.'), '_WT'));

		return $oDictionary->getPluralTranslation($aArgs[0], $aArgs[1], $aArgs[2]);
	}
}

// Logging functions

if (!defined('WEE_LOG_FORMAT'))
	define('WEE_LOG_FORMAT', '[%c] [%2$s] [wee] %1$s');

/**
	Send a message to STDERR.

	The message is formatted using the WEE_LOG_FORMAT constant.
	The constant is first passed through strftime, and then sprintf with the 2 parameters.

	When using CLI, the error will show up in STDERR.
	When using Apache, the error will show up in Apache's error_log file.

	There is absolutely no performance impact for using this function.
	You can basically log anything you want without having to worry about performance.

	@param $sMessage The log message.
	@param $sType The message type. Preferrably one of the Apache error level.
	@see http://php.net/strftime
	@see http://httpd.apache.org/docs/2.0/mod/core.html#loglevel
*/

function weeLog($sMessage, $sType = 'notice')
{
	$sPreviousLocale = setlocale(LC_TIME, 'C');
	$sLog = strftime(WEE_LOG_FORMAT);
	setlocale(LC_TIME, $sPreviousLocale);

	$sLog = sprintf($sLog, $sMessage, $sType);

	file_put_contents('php://stderr', $sLog . "\n");
}

// Other useful functions

/**
	Returns the array value if it exists, else a default value.
	Simpler form than using the conditional operators, and returns null by default, which we usually want.

	@param	$aArray		The array.
	@param	$sKey		The key to look for in the array.
	@param	$mIfNotSet	The default value.
	@return	mixed
*/

function array_value($aArray, $sKey, $mIfNotSet = null)
{
	if (isset($aArray[$sKey]))
		return $aArray[$sKey];
	return $mIfNotSet;
}

/**
	Remove a directory and all its contents.

	@param	$sPath					Path to the directory to remove.
	@param	$bOnlyContents			Boolean to check if the directory is to be left in place.
	@throw	FileNotFoundException	$sPath is not a directory.
	@throw	NotPermittedException	$sPath cannot be removed because of insufficient file permissions.
*/

function rmdir_recursive($sPath, $bOnlyContents = false)
{
	is_dir($sPath) or burn('FileNotFoundException', "'$sPath' is not a directory.");

	$r = @opendir($sPath)
		or burn('NotPermittedException', "'$sPath' directory cannot be opened.");

	while (($s = readdir($r)) !== false)
		if ($s != '.' && $s !== '..') {
			$s = $sPath . '/' . $s;
			if (is_dir($s) && !is_link($s))
				rmdir_recursive($s);
			else {
				@unlink($s)
					or burn('NotPermittedException', "'$s' file cannot be deleted.");
			}
		}

	closedir($r);

	if (!$bOnlyContents) {
		@rmdir($sPath)
			or burn('NotPermittedException', "'$sPath' directory cannot be deleted.");
	}
}

/**
	Convert special characters to XML entities.

	Original author: treyh on PHP comments for htmlspecialchars.

	@param	$sText	The string being converted.
	@return	string	The converted string.
*/

function xmlspecialchars($sText)
{
	return str_replace('&#039;', '&apos;', htmlspecialchars($sText, ENT_QUOTES, 'utf-8'));
}

// Core components

/**
	Interface for mappable objects.
	Mappable are objects that can be mapped to an array using the toArray method.
*/

interface Mappable
{
	public function toArray();
}

/**
	Interface for printable objects.
	Printable objects are objects that can be converted to string using the toString method.

	We have to use this instead of __toString since we can't throw any exception in __toString...
*/

interface Printable
{
	public function toString();
}

require(WEE_PATH . 'weeAutoload' . CLASS_EXT);
weeAutoload::addPath(WEE_PATH);

require(WEE_PATH . 'exceptions/weeException' . CLASS_EXT);
