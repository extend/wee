<?php

/*
	Web:Extend
	Copyright (c) 2008 Dev:Extend

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
	Exception thrown when a comparison test fails in a unit test case.
*/

class ComparisonTestException extends UnitTestException
{
	/**
		The expected value.
	*/

	protected $mExpected;

	/**
		The actual value returned by the test.
	*/

	protected $mActual;

	/**
		Constructs a new ComparisonTestException.

		@param	$sMessage	The message of the exception.
		@param	$mExpected	The expected value.
		@param	$mActual	The actual value returned by the test.
	*/

	public function __construct($mExpected, $mActual, $sMessage)
	{
		parent::__construct($sMessage);

		$this->mExpected	= $mExpected;
		$this->mActual		= $mActual;
	}

	/**
		Returns the actual value returned by the test.

		@return	mixed		The actual value returned by the test.
	*/

	public function getActual()
	{
		return $this->mActual;
	}

	/**
		Returns the expected value.

		@return	mixed		The expected value.
	*/

	public function getExpected()
	{
		return $this->mExpected;
	}
}
