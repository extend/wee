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
	Test the condition, and throws the specified exception if the condition SUCCEED. Else, do nothing.

	Use this function for simpler checks in your code.
	Usually, you don't have to continue rendering the page when there's an error in the page URL for example (because of an error or a hack attempt),
	you should just stop the script and print an error page telling the user that the URL wasn't entered correctly.
	This also apply for database errors, where you die with an error page if the database is down.
	This function allows you to throw exception when this happens, and if you don't catch the exception
	the class weeException will print an error page.

	@deprecated Use "condition or burn()" or "condition and burn()" instead of fire.
	@param $bCondition The condition to check
	@param $sException The exception class to throw if the condition SUCCEED.
	@param $sMessage Description of the error and, when applicable, how to resolve it.
*/

function fire($bCondition, $sException = 'UnexpectedValueException', $sMessage = null)
{
	if ($bCondition)
		burn($sException, $sMessage);
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

	public static function formatTrace($aTrace)
	{
		$sTraceAsString = '';

		$iFrom = 0 + (int)(empty($aTrace[0]['class']) && $aTrace[0]['function'] == 'burn')
			+ 2 * (array_value($aTrace[0], 'class') == 'weeException' && array_value($aTrace[0], 'function') == 'handleError');

		for ($i = $iFrom; $i < count($aTrace); $i++) {
			$sTraceAsString .= '#' . ($i - $iFrom) . ' ';
			if (empty($aTrace[$i]['file']))
				$sTraceAsString .= '(PHP internals): ';
			else
				$sTraceAsString .= array_value($aTrace[$i], 'file', '?') . '(' . array_value($aTrace[$i], 'line', '?') . '): ';
			$sTraceAsString .= array_value($aTrace[$i], 'class') . array_value($aTrace[$i], 'type') . $aTrace[$i]['function'] . '(';

			if (!empty($aTrace[$i]['args'])) {
				foreach ($aTrace[$i]['args'] as $mArg)
					$sTraceAsString .= getType($mArg) . ', ';

				$sTraceAsString = substr($sTraceAsString, 0, -2);
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

		@param	$iNumber	Contains the level of the error raised, as an integer.
		@param	$sMessage	Contains the error message, as a string.
		@param	$sFile		Contains the filename that the error was raised in, as a string.
		@param	$iLine		Contains the line number the error was raised at, as an integer.
		@see http://php.net/set_error_handler
	*/

	public static function handleError($iNumber, $sMessage, $sFile, $iLine)
	{
		// Return directly if @ was used: this error has been masked.
		if (error_reporting() == 0)
			return;

		$sName = self::getLevelName($iNumber);

		if (defined('WEE_CLI'))
			self::printError('Error: ' . $sName . "\n"
				. 'Message: ' . $sMessage . "\n"
				. "Trace:\n" . self::formatTrace(debug_backtrace()));
		else
			self::printErrorPage(array(
				'type'		=> 'error',
				'name'		=> $sName,
				'number'	=> $iNumber,
				'message'	=> $sMessage,
				'trace'		=> self::formatTrace(debug_backtrace()),
				'file'		=> $sFile,
				'line'		=> $iLine,
			));

		exit($iNumber);
	}

	/**
		Function called when an exception is thrown and isn't catched by the script.

		It gets the exception's details and send them to the error page.
		It does not stop the script execution, since PHP does it itself after calling this function.

		@param $oException The exception object.
		@see http://php.net/set_exception_handler
	*/

	public static function handleException($oException)
	{
		if (defined('WEE_CLI'))
			self::printError('Exception: ' . get_class($oException) . "\n"
				. 'Message: ' . $oException->getMessage() . "\n"
				. "Trace:\n" . self::formatTrace($oException->getTrace()));
		else {
			$aTrace = $oException->getTrace();

			// If burn was used, take the file and line where the burn call occurred
			if (empty($aTrace[0]['class']) && $aTrace[0]['function'] == 'burn')
				$aFileAndLine = array(
					'file'	=> $aTrace[0]['file'],
					'line'	=> $aTrace[0]['line'],
				);
			else
				$aFileAndLine = array(
					'file'	=> $oException->getFile(),
					'line'	=> $oException->getLine(),
				);

			self::printErrorPage(array(
				'type'		=> 'exception',
				'name'		=> get_class($oException),
				'message'	=> $oException->getMessage(),
				'trace'		=> self::formatTrace($oException->getTrace()),
			) + $aFileAndLine);
		}
	}

	/**
		Delete all buffers and print the given error.

		@param $sError The error to print.
	*/

	protected static function printError($sError)
	{
		while (@ob_end_clean()) ;

		echo $sError . "\n";
	}

	/**
		Delete all buffers and print the error page.
		If no page was defined using weeException::setErrorPage, the default error page is shown.

		@param $aDebug An array containing debugging information about the error or the exception.
	*/

	public static function printErrorPage($aDebug)
	{
		while (@ob_end_clean()) ;

		if (empty(self::$sErrorPagePath))
			self::$sErrorPagePath = ROOT_PATH . 'res/wee/error.htm';

		if (defined('WEE_GZIP'))
			ob_start('ob_gzhandler');

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
