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
		Filter a trace returned by an exception.
		Top stack traces from the burn function or other methods of this class are removed from the trace.

		@param	$aTrace	The original trace.
		@return	array	The filtered trace.
	*/

	protected static function filterTrace(array $aTrace)
	{
		while (isset($aTrace[0]['class']) && $aTrace[0]['class'] == __CLASS__ || !isset($aTrace[0]['class']) && $aTrace[0]['function'] == 'burn')
			array_shift($aTrace);
		return $aTrace;
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
				$sArgs = '';
				foreach ($aCall as $mArg)
					$sArgs .= gettype($mArg) . ', ';
				$sTraceAsString .= substr($sArgs, 0, -2);
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
		Function called when an exception is thrown and isn't catched by the script.

		It gets the exception's details and send them to the error page.
		It does not stop the script execution, since PHP does it itself after calling this function.

		If the request is an HTTP request, and:
			- If the exception is an instance of RouteNotFoundException, send a 404 Not Found error
			- If the exception is an instance of NotPermittedException, send a 403 Forbidden error
			- Otherwise, send a 500 Internal Server Error

		When DEBUG is enabled and the request is an HTTP request, send the exception to FirePHP
		to ease debug through Firebug.

		@param $eException The exception object.
		@see http://php.net/set_exception_handler
		@see http://www.firephp.org/
		@see http://getfirebug.com/
	*/

	public static function handleException(Exception $eException)
	{
		try {
			if (defined('WEE_CLI')) {
				$sError = $eException instanceof ErrorException
					? sprintf(_WT('Error: %s'), self::getLevelName($eException->getSeverity()))
					: sprintf(_WT('Exception: %s'), get_class($eException));

				$sError .= "\n" . sprintf(_WT('Message: %s'), $eException->getMessage()) . "\n";
				$sError .= "\n" . _WT('Trace:') . "\n" . self::formatTrace(self::filterTrace($eException->getTrace()));

				self::printError($sError);
			} else {
				if ($eException instanceof RouteNotFoundException)
					header('HTTP/1.0 404 Not Found');
				elseif ($eException instanceof NotPermittedException)
					header('HTTP/1.0 403 Forbidden');
				else
					header('HTTP/1.0 500 Internal Server Error');

				if (defined('DEBUG'))
					FirePHP::getInstance(true)->error($eException);

				$aTrace = self::filterTrace($eException->getTrace());

				if ($eException instanceof ErrorException)
					$aError = array(
						'type'	=> 'error',
						'name'	=> self::getLevelName($eException->getSeverity()),
					);
				else
					$aError = array(
						'type'	=> 'exception',
						'name'	=> get_class($eException),
					);

				if (isset($aTrace[0]['file']))
					$aError += array(
						'file'	=> $aTrace[0]['file'],
						'line'	=> $aTrace[0]['line'],
					);
				else
					$aError += array(
						'file'	=> $eException->getFile(),
						'line'	=> $eException->getLine(),
					);

				self::printErrorPage($aError + array(
					'message'	=> $eException->getMessage(),
					'trace'		=> self::formatTrace($aTrace),
				));
			}
		} catch (Exception $e) {
			if (defined('DEBUG'))
				echo $e;
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
