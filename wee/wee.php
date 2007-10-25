<?php

/*
	Web:Extend
	Copyright (c) 2006 Dev:Extend

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

// Detect whether we are on Windows

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	define('WEE_ON_WINDOWS', 1);

// Detect whether we are using the CLI

if (!empty($_SERVER['argc']))
	define('WEE_CLI', 1);

// Paths and files extensions

if (!defined('BASE_PATH'))
{
	$i = substr_count(substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME'])), '/');
	for ($s = null; $i > 0; $i--)
		$s .= '../';

	define('BASE_PATH', $s);
}
if (!defined('ROOT_PATH'))	define('ROOT_PATH',	'./');
if (!defined('APP_PATH'))
{
	if (ROOT_PATH == './')	define('APP_PATH', BASE_PATH);
	else					define('APP_PATH', BASE_PATH . ROOT_PATH);
}
if (!defined('WEE_PATH'))	define('WEE_PATH', ROOT_PATH . 'wee/');
if (!defined('PHP_EXT'))	define('PHP_EXT',  strrchr(__FILE__, '.'));
if (!defined('CLASS_EXT'))	define('CLASS_EXT',	'.class' . PHP_EXT);

if (defined('DEBUG'))
{
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
}
else
{
	error_reporting(0);
	ini_set('display_errors', 0);
}

// Don't use it
$_REQUEST = array();

// Atomize magic quotes

set_magic_quotes_runtime(0);
if (get_magic_quotes_gpc())
{
	// Note:	stripslashes converts null to empty string -- we may need an alternative here

	function mqs(&$sValue, $sKey) { $sValue = stripslashes($sValue); }
	array_walk_recursive($_GET,		'mqs');
	array_walk_recursive($_POST,	'mqs');
	array_walk_recursive($_COOKIE,	'mqs');
	array_walk_recursive($_FILES,	'mqs');

	// PHP configuration should reflect the fact that magic quotes have been removed

	ini_set('magic_quotes_gpc',		0);
	ini_set('magic_quotes_sybase',	0);
}

// Core components

/**
	PHP namespace emulation.
	Namespaces should be declared as final.
*/

class Namespace { private function __construct() {} }

/**
	Interface for declaring singletons in wee.
*/

interface Singleton { public static function instance(); }

/**
	Interface for printable objects.
*/

interface Printable { public function toString(); }

require(WEE_PATH . 'weeAutoload' . CLASS_EXT);
require(WEE_PATH . 'exceptions/weeException' . CLASS_EXT);

weeAutoload::addPath(WEE_PATH);

// PHP Functions/Extensions emulation

if (!function_exists('ctype_alnum'))
	require(WEE_PATH . 'emul_ctype' . PHP_EXT);
require(WEE_PATH . 'emul_php' . PHP_EXT);

// Dummy _() if it doesn't exist

if (!function_exists('_'))
{
	function _($s) { return $s; }
}

?>
