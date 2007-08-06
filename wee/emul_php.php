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
	Returns the array value if exists, else a default value.
	Simpler form than using the conditional operators, and returns null by default, which we usually want.
*/

function array_value($aArray, $sKey, $mIfNotSet = null)
{
	if (isset($aArray[$sKey]))
		return $aArray[$sKey];
	return $mIfNotSet;
}

if (!function_exists('date_default_timezone_get'))
{
	/**
		Emulation of PHP's date_default_timezone_get.
		TODO: Not tested yet. Need feedback.

		@see http://php.net/date_default_timezone_get
	*/

	function date_default_timezone_get()
	{
		$sTimezone = getenv('TZ');
		if (empty($sTimezone))
			return 'UTC';
		return $sTimezone;
	}

	/**
		Emulation of PHP's date_default_timezone_get.
		TODO: Not tested yet. Need feedback.

		@see http://php.net/date_default_timezone_set
	*/

	function date_default_timezone_set($sTimezone)
	{
		putenv('TZ=' . $sTimezone);
	}
}

if (version_compare(phpversion(), '5.1.0', '<'))
{
	/**
		Exception thrown when a method call was illegal.
	*/

	class BadMethodCallException extends BadFunctionCallException
	{
	}

	/**
		Exception thrown when a function call was illegal.
	*/

	class BadFunctionCallException extends LogicException
	{
	}

	/**
		Exception that denotes a value not in the valid (mathematical) domain was used.
	*/

	class DomainException extends LogicException
	{
	}

	/**
		Exception that denotes invalid arguments were passed.
	*/

	class InvalidArgumentException extends LogicException
	{
	}

	/**
		Exception thrown when a parameter exceeds the allowed length (for strings, arrays, files...).
	*/

	class LengthException extends LogicException
	{
	}

	/**
		Exception that represents error in the program logic.
	*/

	class LogicException extends Exception
	{
	}

	/**
		Exception thrown when an illegal index was requested (when it can't be detected at compile time).
	*/

	class OutOfBoundsException extends RuntimeException
	{
	}

	/**
		Exception thrown when an illegal index was requested (when it can be detected at compile time).
	*/

	class OutOfRangeException extends LogicException
	{
	}

	/**
		Exception thrown to indicate arithmetic/buffer overflow.
	*/

	class OverflowException extends RuntimeException
	{
	}

	/**
		Exception thrown to indicate range errors during program execution (runtime version of DomainException, and not over/underflow exceptions).
	*/

	class RangeException extends RuntimeException
	{
	}

	/**
		Exception thrown for errors that are only detectable at runtime.
	*/

	class RuntimeException extends Exception
	{
	}

	/**
		Exception thrown to indicate arithmetic/buffer underflow.
	*/

	class UnderflowException extends RuntimeException
	{
	}

	/**
		Exception thrown to indicate an unexpected value.
	*/

	class UnexpectedValueException extends RuntimeException
	{
	}

	/**
		Format line as CSV and write to file pointer.

		@see		http://php.net/fputcsv
		@warning	Not tested yet.
	*/

	function fputcsv($rHandle, array $aFields, $sDelimiter = null, $sEnclosure = null)
	{
		if ($sDelimiter === null)
			$sDelimiter = ',';
		if ($sEnclosure === null)
			$sEnclosure = '"';

		$sLine = '';
		foreach ($aFields as $mField)
		{
			if (strpos($mField, $sEnclosure) !== null)
				$mField = str_replace(
					array('\\',		$sEnclosure),
					array('\\\\',	'\\' . $sEnclosure),
					$mField
				);

			if (strpos($mField, $sDelimiter) !== null)
				$mField = $sEnclosure . $mField . $sEnclosure;

			$sLine .= $mField . $sDelimiter;
		}
		$sLine = substr($sLine, 0, - 1);

		return fwrite($rHandle, $sLine);
	}
}

?>
