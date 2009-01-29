<?php

/*
	Web:Extend
	Copyright (c) 2006-2009 Dev:Extend

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

	@param $sException The class of the exception to throw.
	@param $sMessage Message describing the error and how to resolve it.
*/

function burn($sException, $sMessage = null)
{
	if (class_exists($sException))
		throw new $sException($sMessage);
	throw new DoubleFaultException('Class not found: ' . $sException);
}

/**
	Namespace for exception handling when the exception thrown is not catched.
	You should never need to call these functions yourself.
*/

final class weeException
{
	/**
		Path to a custom error page.
	*/

	protected static $sErrorPagePath;

	/**
		Namespace.
	*/

	private function __construct()
	{
	}

	/**
		Format the trace in a way similar to Exception::getTraceAsString but compatible
		with both Exception::getTrace and debug_backtrace. Extraneous information is
		automatically removed making the resulting string identical for both types of errors.

		@param $aTrace The trace array returned by either Exception::getTrace or debug_backtrace.
		@return string The trace formatted as string.
	*/

	public static function formatTrace(array $aTrace)
	{
		$sTraceAsString = '';
		while (isset($aTrace[0]['class']) ? $aTrace[0]['class'] == __CLASS__ : $aTrace[0]['function'] == 'burn')
			array_shift($aTrace);

		foreach ($aTrace as $i => $aCall) {
			$sTraceAsString .= '#' . $i . ' ';
			if (isset($aCall['file']))
				$sTraceAsString .= $aCall['file'] . '(' . $aCall['line'] . '): ';
			else
				$sTraceAsString .= '(PHP internals): ';

			if (isset($aCall['class']))
				$sTraceAsString .= $aCall['class'] . $aCall['type'];
			$sTraceAsString .= $aCall['function'] . '(';

			if (!empty($aCall['args'])) {
				foreach ($aCall as $mArg)
					$sTraceAsString .= gettype($mArg) . ', ';
				$sTraceAsString .= substr($sTraceAsString, 0, -2);
			}

			$sTraceAsString .= ")\n";
		}

		return substr($sTraceAsString, 0, -1);
	}

	/**
		Returns the name of a error level, or "Unknown PHP Error" if the error is not known.

		@param	$iLevel				The error level.
		@return	string				The name of the error level.
	*/

	public static function getLevelName($iLevel)
	{
		static $aTypes = array(
			E_ERROR				=> 'E_ERROR',
			E_WARNING			=> 'E_WARNING',
			E_PARSE				=> 'E_PARSE',
			E_NOTICE			=> 'E_NOTICE',
			E_CORE_ERROR		=> 'E_CORE_ERROR',
			E_CORE_WARNING		=> 'E_CORE_WARNING',
			E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
			E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
			E_USER_ERROR		=> 'E_USER_ERROR',
			E_USER_WARNING		=> 'E_USER_WARNING',
			E_USER_NOTICE		=> 'E_USER_NOTICE',
			E_STRICT			=> 'E_STRICT',
			E_RECOVERABLE_ERROR	=> 'E_RECOVERABLE_ERROR',
			E_ALL				=> 'E_ALL'
		);

		return isset($aTypes[$iLevel]) ? $aTypes[$iLevel] : 'Unknown PHP Error';
	}

	/**
		Function called when a PHP error is triggered.

		It gets the error's details and send them to the error page, then stops the execution.

		If the request is an HTTP request, a 500 Internal Server Error code is sent.

		@param	$iLevel		Contains the level of the error raised, as an integer.
		@param	$sMessage	Contains the error message, as a string.
		@param	$sFile		Contains the filename that the error was raised in, as a string.
		@param	$iLine		Contains the line number the error was raised at, as an integer.
		@see http://php.net/set_error_handler
	*/

	public static function handleError($iLevel, $sMessage, $sFile, $iLine)
	{
		// Return directly if @ was used: this error has been masked.
		if (error_reporting() == 0)
			return;
		throw new ErrorException($sMessage, 0, $iLevel, $sFile, $iLine);
	}

	/**
		Function called when an ErrorException has been caught by the exception handler.

		@param	$eException	The ErrorException instance.
		@see	http://php.net/errorexception
	*/

	public static function handleErrorException(ErrorException $eException)
	{
		$sName	= self::getLevelName($eException->getSeverity());
		$sTrace	= self::formatTrace($eException->getTrace());

		if (defined('WEE_CLI'))
			self::printError('Error: ' . $sName . "\n"
				. 'Message: ' . $eException->getMessage() . "\n"
				. "Trace:\n" . $sTrace);
		else {
			header('HTTP/1.0 500 Internal Server Error');

			self::printErrorPage(array(
				'type'		=> 'error',
				'name'		=> $sName,
				'number'	=> $eException->getSeverity(),
				'message'	=> $eException->getMessage(),
				'trace'		=> $aTrace,
				'file'		=> $eException->getFile(),
				'line'		=> $eException->getLine(),
			));
		}
	}

	/**
		Function called when an exception is thrown and isn't catched by the script.

		It gets the exception's details and send them to the error page.
		It does not stop the script execution, since PHP does it itself after calling this function.

		If the request is an HTTP request, and:
			- If the exception is an instance of RouteNotFoundException, send a 404 Not Found error
			- If the exception is an instance of NotPermittedException, send a 403 Forbidden error
			- Otherwise, send a 500 Internal Server Error

		@param $eException The exception object.
		@see http://php.net/set_exception_handler
	*/

	public static function handleException(Exception $eException)
	{
		if ($eException instanceof ErrorException)
			return self::handleErrorException($eException);

		if (defined('WEE_CLI'))
			self::printError('Exception: ' . get_class($eException) . "\n"
				. 'Message: ' . $eException->getMessage() . "\n"
				. "Trace:\n" . self::formatTrace($eException->getTrace()));
		else {
			if ($eException instanceof RouteNotFoundException)
				header('HTTP/1.0 404 Not Found');
			elseif ($eException instanceof NotPermittedException)
				header('HTTP/1.0 403 Forbidden');
			else
				header('HTTP/1.0 500 Internal Server Error');

			$aTrace = $eException->getTrace();

			// If burn was used, take the file and line where the burn call occurred
			if (empty($aTrace[0]['class']) && $aTrace[0]['function'] == 'burn')
				$aFileAndLine = array(
					'file'	=> $aTrace[0]['file'],
					'line'	=> $aTrace[0]['line'],
				);
			else
				$aFileAndLine = array(
					'file'	=> $eException->getFile(),
					'line'	=> $eException->getLine(),
				);

			self::printErrorPage(array(
				'type'		=> 'exception',
				'name'		=> get_class($eException),
				'message'	=> $eException->getMessage(),
				'trace'		=> self::formatTrace($eException->getTrace()),
			) + $aFileAndLine);
		}
	}

	/**
		Delete all buffers and print the given error.

		@param $sError The error to print.
	*/

	protected static function printError($sError)
	{
		while (ob_get_level())
			ob_end_clean();
		echo $sError . "\n";
	}

	/**
		Delete all buffers and print the error page.
		If no page was defined using weeException::setErrorPage, the default error page is shown.

		@param $aDebug An array containing debugging information about the error or the exception.
	*/

	public static function printErrorPage($aDebug)
	{
		while (ob_get_level())
			ob_end_clean();

		if (empty(self::$sErrorPagePath))
			self::$sErrorPagePath = ROOT_PATH . 'res/wee/error.htm';

		// Restart the gzip handler if it was started before
		if (defined('WEE_GZIP'))
			ob_start('ob_gzhandler');

		// Switch output to XHTML and encode the debug array
		weeOutput::select(new weeXHTMLOutput);
		$aDebug = weeOutput::instance()->encodeArray($aDebug);

		require(self::$sErrorPagePath);
	}

	/**
		Defines a custom error page to be shown when an error or an exception occur.
		The page can be a PHP script, an HTML page or a plain-text file.

		The $aDebug array is available in the code of this page. You can check if
		DEBUG is defined and print the debug information if needed.

		The $aDebug array contains the following values:
			* type: either 'error' or 'exception'
			* name: name of the error/exception
			* message: message associated with it
			* trace: complete trace leading to the uncatched exception
			* file: the file where the error occurred
			* line: the line where the error occurred

		An error also has this value:
			* number: error's number

		@param $sPath The path to the new error page.
	*/

	public static function setErrorPage($sPath)
	{
		self::$sErrorPagePath = $sPath;
	}
}

// Set handlers

set_error_handler(array('weeException', 'handleError'));
set_exception_handler(array('weeException', 'handleException'));
