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

if (version_compare(phpversion(), '5.1.0', '<'))
{
	class LogicException				extends Exception					{} // Exception that represents error in the program logic
	class BadFunctionCallException		extends LogicException				{} // Exception thrown when a function call was illegal
	class BadMethodCallException		extends BadFunctionCallException	{} // Exception thrown when a method call was illegal
	class DomainException				extends LogicException				{} // Exception that denotes a value not in the valid (mathematical) domain was used
	class InvalidArgumentException		extends LogicException				{} // Exception that denotes invalid arguments were passed
	class LengthException				extends LogicException				{} // Exception thrown when a parameter exceeds the allowed length (for strings, arrays, files...)
	class OutOfRangeException			extends LogicException				{} // Exception thrown when an illegal index was requested (when it can be detected at compile time)

	class RuntimeException				extends Exception					{} // Exception thrown for errors that are only detectable at runtime
	class OutOfBoundsException			extends RuntimeException			{} // Exception thrown when an illegal index was requested (when it can't be detected at compile time)
	class OverflowException				extends RuntimeException			{} // Exception thrown to indicate arithmetic/buffer overflow
	class RangeException				extends RuntimeException			{} // Exception thrown to indicate range errors during program execution (runtime version of DomainException, and not over/underflow exceptions)
	class UnderflowException			extends RuntimeException			{} // Exception thrown to indicate arithmetic/buffer underflow
	class UnexpectedValueException		extends RuntimeException			{} // Exception thrown to indicate an unexpected value
}

	class BadXMLException				extends LogicException				{} // Exception thrown when an XML doesn't follow strictly its DTD schema
	class DoubleFaultException			extends LogicException				{} // Exception thrown in the exception handling code
	class FileNotFoundException			extends LogicException				{} // Exception thrown when a required file is missing
	class IllegalStateException			extends LogicException				{} // Exception thrown when a method is called and the object isn't in the right state (example: not initialized)

	class ConfigurationException		extends RuntimeException			{} // Exception thrown when a configuration requirement is not met
	class DatabaseException				extends RuntimeException			{} // Exception thrown when there is a database error
	class NotPermittedException			extends RuntimeException			{} // Exception thrown when an user try to do something he doesn't have permission to
	class ValidatorException			extends RuntimeException			{} // Exception thrown when a validator fails

function burn($s)
{
	if (class_exists($s, false))
		throw new $s;
	throw new DoubleFaultException;
}

function fire($b, $s = 'UnexpectedValueException')
{
	if ($b) burn($s);
}

final class weeException extends Namespace
{
	public static function handleError($iNumber, $sMessage, $sFile, $iLine)
	{
		if (error_reporting() == 0) return;

		$aTypes = array(
			1 => 'E_ERROR',
			2 => 'E_WARNING',
			4 => 'E_PARSE',
			8 => 'E_NOTICE',
			16 => 'E_CORE_ERROR',
			32 => 'E_CORE_WARNING',
			64 => 'E_COMPILE_ERROR',
			128 => 'E_COMPILE_WARNING',
			256 => 'E_USER_ERROR',
			512 => 'E_USER_WARNING',
			1024 => 'E_USER_NOTICE',
			2048 => 'E_STRICT',
			4096 => 'E_RECOVERABLE_ERROR',
			8191 => 'E_ALL',
		);

		$sDebug = null;

		if (defined('DEBUG'))
		{
			$sDebug .= '</div><div id="php"><h2>' . str_replace("<a href='", "<a href='http://php.net/", $sMessage) . '</h2>';
			$sDebug .= '<h3>Type:</h3><span>' . $aTypes[$iNumber] . ' (' . $iNumber . ')</span><br/>';
			$sDebug .= '<h3>File:</h3><span>' . $sFile . '</span><br/>';
			$sDebug .= '<h3>Line:</h3><span>' . $iLine . '</span><br/>';
		}

		self::printErrorPage($sDebug);
		self::logError('error', $sFile, $iLine, (empty($aTypes[$iNumber])) ? $aTypes[1] : $aTypes[$iNumber], $sMessage);
		exit($iNumber);
	}

	public static function handleException($o)
	{
		$sDebug = null;

		if (defined('DEBUG'))
		{
			$sDebug .= '</div><div id="exception"><h2>' . get_class($o) . '</h2>';
			$sDebug .= '<h3>Trace:</h3><p>' . nl2br($o->getTraceAsString()) . '</p>';

			//TODO:change function name
			//TODO:error should be inside exception!
			if ($o instanceof DatabaseException && !empty($GLOBALS['Db']) && $GLOBALS['Db'] instanceof weeDatabase && $GLOBALS['Db']->getLastError() != null)
				$sDebug .= '<h3>Error:</h3><p>' . $GLOBALS['Db']->getLastError() . '</p>';
		}

		self::printErrorPage($sDebug);
		self::logError('exception', $o->getFile(), $o->getLine(), get_class($o), $o->getTraceAsString());
	}

	public static function logError($sClass, $sFile, $iLine, $sType, $sMessage)
	{
		if (!defined('LOG_PATH')) return;

		$r = @fopen(@tempnam(LOG_PATH, 'error-'), 'w');
		if ($r)
		{
			@fwrite($r, time() . "\n");
			foreach (@func_get_args() as $s)
				@fwrite($r, $s . "\n");
			@fclose($r);
		}
	}

	public static function printErrorPage($sDebug)
	{
		while (@ob_end_clean()) ;

		//TODO:translatable messages
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html><head><title>Fatal error</title>' .
			 '<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/><link rel="stylesheet" type="text/css" media="all" href="' . BASE_PATH . ROOT_PATH . 'css/error.css"/></head><body><div id="error"><h1>' .
			 'Oops! An error occurred.</h1><p>The page you tried to access is currently unavailable. This can happen for one of the following reason:</p><ul><li>' .
			 'The Web address you entered is invalid or incomplete. Please check that you typed it correctly.</li><li>' .
			 'The server is too busy. Please wait a moment and try to reload the page later.</li><li>' .
			 'The page you try to access may have been removed and doesn\'t exist anymore. Please try to <a href="/">browse</a> for it.' .
			 '</li></ul><p>You can also try to <a href="javascript:history.back()">go back</a> where you came from.</p>' .
			 $sDebug . '</div></body></html>';
	}
}

// Using the array technique didn't work...
function weeErrorHandler($iNumber, $sMessage, $sFile, $iLine)	{ weeException::handleError($iNumber, $sMessage, $sFile, $iLine); }
function weeExceptionHandler($o)								{ weeException::handleException($o); }

set_error_handler('weeErrorHandler');
set_exception_handler('weeExceptionHandler');

?>
