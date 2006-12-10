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

/**
	Throws an exception of the class specified in argument if exists, else throws DoubleFaultException.

	@param $s The class of the exception to throw.
*/

function burn($sException)
{
	if (class_exists($sException))
		throw new $sException;
	throw new DoubleFaultException;
}

/**
	Test the condition, and throws the specified exception if the condition SUCCEED. Else, do nothing.

	Use this function for simpler checks in your code.
	Usually, you don't have to continue rendering the page when there's an error in the page URL for example (because of an error or a hack attempt),
	you should just stop the script and print an error page telling the user that the URL wasn't entered correctly.
	This also apply for database errors, where you die with an error page if the database is down.
	This function allows you to throw exception when this happens, and if you don't catch the exception
	the class weeException will print the standard error page. You can define your error handler by using set_error_handler and set_exception_handler.

	@param	$bCondition	The condition to check
	@param	$sException	The condition to throw if the condition SUCCEED.
*/

function fire($bCondition, $sException = 'UnexpectedValueException')
{
	if ($bCondition)
		burn($sException);
}

/**
	Namespace for exception handling when the exception thrown is not catched.
	You should never need to call these functions yourself.
*/

final class weeException extends Namespace
{
	/**
		Function called when a PHP error is triggered.
		Gets error details in DEBUG mode, then prints the error page and log error if possible.
		Then stops script execution.

		@param	$iNumber	Contains the level of the error raised, as an integer.
		@param	$sMessage	Contains the error message, as a string.
		@param	$sFile		Contains the filename that the error was raised in, as a string.
		@param	$iLine		Contains the line number the error was raised at, as an integer.
		@see http://php.net/set_error_handler
	*/

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

	/**
		Function called when an exception is thrown and isn't catched by the script.
		Gets exception details in DEBUG mode, then prints the error page and log error if possible.
		It does not stop the script execution, since PHP does it itself after calling this function.

		@param $oException The exception object.
		@see http://php.net/set_exception_handler
	*/

	public static function handleException($oException)
	{
		$sDebug = null;

		if (defined('DEBUG'))
		{
			$sDebug .= '</div><div id="exception"><h2>' . get_class($oException) . '</h2>';
			$sDebug .= '<h3>Trace:</h3><p>' . nl2br($oException->getTraceAsString()) . '</p>';

			//TODO:change function name
			//TODO:error should be inside exception!
			if ($oException instanceof DatabaseException && !empty($GLOBALS['Db']) && $GLOBALS['Db'] instanceof weeDatabase && $GLOBALS['Db']->getLastError() != null)
				$sDebug .= '<h3>Error:</h3><p>' . $GLOBALS['Db']->getLastError() . '</p>';
		}

		self::printErrorPage($sDebug);
		self::logError('exception', $oException->getFile(), $oException->getLine(), get_class($oException), $oException->getTraceAsString());
	}

	/**
		Logs the specified error to a file in the LOG_PATH path, if defined.

		The filename is randomly created, thus the filename order isn't the same as the creation order.

		@param	$sClass		Contains either 'error' or 'exception'.
		@param	$sFile		Contains the filename that the error was raised in, as a string.
		@param	$iLine		Contains the line number the error was raised at, as an integer.
		@param	$sType		Contains either the level of the error raised, or the exception class name, both as strings.
		@param	$sMessage	Contains the error message, as a string.
	*/

	public static function logError($sClass, $sFile, $iLine, $sType, $sMessage)
	{
		if (!defined('LOG_PATH')) return;

		$r = @fopen(@tempnam(LOG_PATH, 'error-'), 'w');
		if ($r)
		{
			@fwrite($r, @time() . "\n");
			foreach (@func_get_args() as $s)
				@fwrite($r, $s . "\n");
			@fclose($r);
		}
	}

	/**
		Prints a default error page, containing some instructions for the user, and some debug information in DEBUG mode.
	*/

	public static function printErrorPage($sDebug)
	{
		while (@ob_end_clean()) ;

		//TODO:translatable messages
		//TODO:inline css, no separate file
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

/**
	Default error handler.
	Defined because passing an array (class, method) to set_error_handler didn't work.

	@see http://php.net/set_error_handler
*/

function weeErrorHandler($iNumber, $sMessage, $sFile, $iLine)
{
	weeException::handleError($iNumber, $sMessage, $sFile, $iLine);
}

/**
	Default exception handler.
	Defined because passing an array (class, method) to set_error_handler didn't work.

	@see http://php.net/set_exception_handler
*/

function weeExceptionHandler($oException)
{
	weeException::handleException($oException);
}

// Set handlers

set_error_handler('weeErrorHandler');
set_exception_handler('weeExceptionHandler');

?>
