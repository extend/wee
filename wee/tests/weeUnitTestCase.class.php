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
	Base class for unit test case.

	Most of the time it is not needed to use it manually, since weeTestSuite does everything for you.
*/

abstract class weeUnitTestCase
{
	/**
		Check whether $mVarLeft == $mVarRight.

		@param $sMessage Error message if test returns false.
	*/

	protected function isEqual($mVarLeft, $mVarRight, $sMessage)
	{
		if ($mVarLeft != $mVarRight)
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether $mVar is false.

		@param $sMessage Error message if test returns false.
	*/

	protected function isFalse($mVar, $sMessage)
	{
		if ((bool)$mVar)
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether $mVarLeft === $mVarRight.

		@param $sMessage Error message if test returns false.
	*/

	protected function isIdentical($mVarLeft, $mVarRight, $sMessage)
	{
		if ($mVarLeft !== $mVarRight)
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether $oObject is an instance of $sClass.

		@param $sMessage Error message if test returns false.
	*/

	protected function isInstanceOf($oObject, $sClass, $sMessage)
	{
		if (!($oObject instanceof $sClass))
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether pattern $sPattern is found in $sSubject.

		@param $sMessage Error message if test returns false.
	*/

	protected function isMatching($sSubject, $sPattern, $sMessage)
	{
		if (0 === preg_match($sSubject, $sPattern))
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether $mVarLeft != $mVarRight.

		@param $sMessage Error message if test returns false.
	*/

	protected function isNotEqual($mVarLeft, $mVarRight, $sMessage)
	{
		if ($mVarLeft == $mVarRight)
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether $mVarLeft !== $mVarRight.

		@param $sMessage Error message if test returns false.
	*/

	protected function isNotIdentical($mVarLeft, $mVarRight, $sMessage)
	{
		if ($mVarLeft === $mVarRight)
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether $oObject is NOT an instance of $sClass.

		@param $sMessage Error message if test returns false.
	*/

	protected function isNotInstanceOf($oObject, $sClass, $sMessage)
	{
		if ($oObject instanceof $sClass)
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether pattern $sPattern is NOT found in $sSubject.

		@param $sMessage Error message if test returns false.
	*/

	protected function isNotMatching($sSubject, $sPattern, $sMessage)
	{
		if (1 === preg_match($sSubject, $sPattern))
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether $mVar is NOT null.

		@param $sMessage Error message if test returns false.
	*/

	protected function isNotNull($mVar, $sMessage)
	{
		if (is_null($mVar))
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether $mVar is null.

		@param $sMessage Error message if test returns false.
	*/

	protected function isNull($mVar, $sMessage)
	{
		if (!is_null($mVar))
			throw new UnitTestException($sMessage);
	}

	/**
		Check whether $mVar is true.

		@param $sMessage Error message if test returns false.
	*/

	protected function isTrue($mVar, $sMessage)
	{
		if (!(bool)$mVar)
			throw new UnitTestException($sMessage);
	}

	/**
		Fails a test.

		@param $sMessage Error message.
	 */

	protected function fail($sMessage)
	{
		throw new UnitTestException($sMessage);
	}

	/**
		Runs this unit test case.

		@return bool True if test completed, false it must be skipped.
	*/

	abstract public function run();
}
