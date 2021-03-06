<?php

/*
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

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
if (version_compare(phpversion(), '5.2.0', '<')) die('PHP 5.2.x or greater is required.');

// Prevent the script from using the default value for MAGIC_STRING

!defined('MAGIC_STRING') or MAGIC_STRING != 'This is a magic string used to salt various hash throughout the framework.'
	or die('The constant MAGIC_STRING defined in your script is using the default value. Please change its value before retrying.');

// Enable error reporting; errors are displayed depending on DEBUG.
// Note that we always keep the environment strict even without DEBUG
// because some features rely on error_reporting being non-null.
// Technically, at least E_WARNING is required, but better be safe than sorry.

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', (int)defined('DEBUG'));

// Define the framework's version
// Its format is compatible with PHP's version_compare function

define('WEE_VERSION', '0.6.0');

// Detect whether we are on Windows

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	define('WEE_ON_WINDOWS', 1);

// Detect whether we are using the CLI

if (PHP_SAPI == 'cli')
	define('WEE_CLI', 1);

// Whether the current request is done through the HTTPS protocol.

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
	define('WEE_HTTPS', 1);

// Paths

if (!defined('BASE_PATH')) // Base path of boostrap file
	define('BASE_PATH', str_repeat('../', substr_count(substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME'])), '/')
		- (int)(isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL'] != $_SERVER['PHP_SELF'])));

if (!defined('ROOT_PATH')) // Path to application's root directory
	define('ROOT_PATH', './');

// Path to application's web directory
if (!defined('APP_PATH')) {
	if (defined('WEE_CLI'))
		define('APP_PATH', BASE_PATH . (ROOT_PATH == './' ? '' : ROOT_PATH));
	else {
		if (isset($_SERVER['HTTP_ORIGIN']))
			$sOrigin = $_SERVER['HTTP_ORIGIN'];
		else {
			$sScheme		= 'http';
			$iDefaultPort	= 80;
			if (defined('WEE_HTTPS')) {
				$sScheme		.= 's';
				$iDefaultPort	= 443;
			}

			$sOrigin = $sScheme . '://' . $_SERVER['HTTP_HOST']
				. ($_SERVER['SERVER_PORT'] != $iDefaultPort ? ':' . $_SERVER['SERVER_PORT'] : '');
		}

		$sPath = dirname($_SERVER['SCRIPT_NAME']);
		if ($sPath != '/')
			$sPath .= '/';
		define('APP_PATH', $sOrigin . $sPath);
	}
}

if (!defined('WEE_PATH')) // Path to the framework's directory
	define('WEE_PATH', ROOT_PATH . 'wee/');

// Files extensions

if (!defined('PHP_EXT')) // PHP files extension
	define('PHP_EXT', strrchr(__FILE__, '.'));

if (!defined('CLASS_EXT')) // PHP class files extension
	define('CLASS_EXT', '.class' . PHP_EXT);

// Define the LC_MESSAGES constant if not defined

if (!defined('LC_MESSAGES'))
	define('LC_MESSAGES', 5);

// Atomize magic quotes

if (get_magic_quotes_runtime())
	@set_magic_quotes_runtime(0);
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
		static $sLocale = 'C';
		static $oDictionary = null;

		$sCurrentLocale = setlocale(LC_MESSAGES, 0);
		if ($sLocale != $sCurrentLocale) {
			$sLocale = $sCurrentLocale;
			$oDictionary = null;

			if ($sLocale != 'C') {
				$a = explode('.', $sLocale, 2);
				$sFile = APP_LOCALE_PATH . '/' . $a[0] . '/LC_MESSAGES/' . APP_LOCALE_DOMAIN . '.mo';
				if (file_exists($sFile))
					$oDictionary = new weeGetTextDictionary($sFile);
			}
		}

		$aArgs	= func_get_args();
		$iCount = count($aArgs);

		if ($iCount == 1)
			return $oDictionary !== null ? $oDictionary->getTranslation($aArgs[0]) : $aArgs[0];

		$iCount == 3 or burn('InvalidArgumentException',
			sprintf(_WT('The %s function requires either 1 or 3 arguments.'), '_WT'));

		if ($oDictionary !== null)
			return $oDictionary->getPluralTranslation($aArgs[0], $aArgs[1], $aArgs[2]);
		return $aArgs[2] > 1 ? $aArgs[1] : $aArgs[0];
	}

	/**
		Translate the given string in the current locale using the framework's domain (wee).
		This function do both gettext and ngettext depending on the number of arguments given.
		This function is reserved for internal use.

		@overload _WT($sText) Translate the given text.
		@overload _WT($sText, $sPlural, $iCount) Plural version of text translation.
		@return string The translated text.
	*/

	function _WT()
	{
		static $sLocale = 'C';
		static $oDictionary;

		$sCurrentLocale = setlocale(LC_MESSAGES, 0);
		if ($sLocale != $sCurrentLocale) {
			$sLocale		= $sCurrentLocale;
			$oDictionary	= null;

			if ($sLocale != 'C') {
				$a = explode('.', $sLocale, 2);
				$sFile = ROOT_PATH . 'share/locale/' . $a[0] . '/LC_MESSAGES/wee.mo';
				if (file_exists($sFile))
					$oDictionary = new weeGetTextDictionary($sFile);
			}
		}

		$aArgs	= func_get_args();
		$iCount = count($aArgs);

		if ($iCount == 1)
			return $oDictionary !== null ? $oDictionary->getTranslation($aArgs[0]) : $aArgs[0];

		$iCount == 3 or burn('InvalidArgumentException',
			sprintf(_WT('The %s function requires either 1 or 3 arguments.'), '_WT'));

		if ($oDictionary !== null)
			return $oDictionary->getPluralTranslation($aArgs[0], $aArgs[1], $aArgs[2]);
		return $aArgs[2] > 1 ? $aArgs[1] : $aArgs[0];
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
	Return the array value if it exists, else a default value.
	Simpler form than using the conditional operators, and returns null by default, which we usually want.

	@param	$aArray		The array.
	@param	$sKey		The key to look for in the array.
	@param	$mIfNotSet	The default value.
	@return	mixed		Array value if it exists, else $mIfNotSet.
*/

function array_value($aArray, $sKey, $mIfNotSet = null)
{
	if (isset($aArray[$sKey]))
		return $aArray[$sKey];
	return $mIfNotSet;
}

/**
	Returns the path information with some path translation.
	The path information is the text after the file and before the query string in an URI.
	Example: http://example.com/my.php/This_is_the_path_info/Another_level/One_more?query_string

	@return string The path information.
*/

function safe_path_info()
{
	$sPathInfo = null;

	if (isset($_SERVER['PATH_INFO']))
		$sPathInfo = $_SERVER['PATH_INFO'];
	elseif (isset($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] != $_SERVER['PHP_SELF'])
		$sPathInfo = $_SERVER['ORIG_PATH_INFO'];
	elseif (isset($_SERVER['REDIRECT_URL']))
		$sPathInfo = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']));

	if ($sPathInfo !== null) {
		// We found the path info from either PATH_INFO or PHP_SELF server variables.

		if (empty($_SERVER['QUERY_STRING']) && substr($_SERVER['REQUEST_URI'], -1) == '?')
			// If the query string is empty, but that an interrogation mark has been
			// explicitely included in the request URI, we keep it.
			$sPathInfo .= '?';

		return $sPathInfo;
	}

	// The path info begins after the script name part of the request URI.

	$iScriptLength	= strlen($_SERVER['SCRIPT_NAME']);
	$sName			= basename($_SERVER['SCRIPT_NAME']);
	$iNameLength	= strlen($sName);
	$sPathInfo		= substr($_SERVER['REQUEST_URI'], $iScriptLength - $iNameLength);

	if (substr($sPathInfo, 0, $iNameLength) == $sName)
		$sPathInfo	= substr($sPathInfo, $iNameLength);

	if (!empty($_SERVER['QUERY_STRING'])) {
		// We need to remove the query string from the path info.
		$i = strlen($_SERVER['QUERY_STRING']);
		if (substr($sPathInfo, -$i) == $_SERVER['QUERY_STRING'])
			$sPathInfo = substr($sPathInfo, 0, -$i - 1);
	}

	return urldecode($sPathInfo);
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
	$r = opendir($sPath);
	while (($s = readdir($r)) !== false)
		if ($s != '.' && $s !== '..') {
			$s = $sPath . '/' . $s;
			if (is_dir($s) && !is_link($s))
				rmdir_recursive($s);
			else
				unlink($s);
		}
	closedir($r);

	if (!$bOnlyContents)
		rmdir($sPath);
}

/**
	Send a header to the browser.
	Tentatively prevents HTTP Response Splitting.

	@param $sString		Header string.
	@param $bReplace	Whether to replace any existing header Replace existing header if true.
*/

function safe_header($sString, $bReplace = true)
{
	headers_sent() and burn('IllegalStateException',
		_WT('The HTTP headers have already been sent.'));
	(strpos($sString, "\r") !== false || strpos($sString, "\n") !== false) and burn('UnexpectedValueException',
		_WT('Line breaks are not allowed in headers to prevent HTTP Response Splitting.'));
	strpos($sString, "\0") !== false and burn('UnexpectedValueException',
		_WT('NUL characters are not allowed in headers.'));

	header($sString, $bReplace);
}

/**
	Start the session.
	The session is reinitialized if the name of the session is invalid.
*/

function safe_session_start()
{
	if (isset($_COOKIE[session_name()]) && !preg_match('/^[a-z0-9-]+$/is', $_COOKIE[session_name()])) {
		unset($_COOKIE[session_name()]);
		setcookie(session_name(), '');
	}

	session_start();
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

/**
	Convert special XML entities back to characters.

	This function is the opposite of htmlspecialchars(). It converts special HTML
	entities back to characters.

	@param	$sText	The text to decode.
	@return	string	The decoded text.
*/

function xmlspecialchars_decode($sText)
{
	return htmlspecialchars_decode(str_replace('&apos;', '&#039;', $sText), ENT_QUOTES);
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
