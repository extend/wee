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
	Automated unit testing.

	Unit test cases that return false value will be ignored.
	Use it if you need additional files that are not unit test cases.
*/

abstract class weeTestSuite implements Printable
{
	/**
		Path to the unit test cases.
	*/

	protected $sTestsPath;

	/**
		Arrays containing the results of the unit test suite, after its completion.
	*/

	protected $aResults = array();

	/**
		Initialize the test suite by setting the path to the unit test cases.

		@param $sTestsPath Path to the unit test cases.
	*/

	public function __construct($sTestsPath)
	{
		$this->sTestsPath = $_SERVER['PWD'] . '/' . $sTestsPath;
	}

	/**
		Add a result to the result array.
		Results can be true or an array containing exception class name and message.

		@param $sFile Filename of the unit test case.
		@param $mResult Result of the unit test case.
	*/

	protected function addResult($sFile, $mResult)
	{
		$this->aResults[$sFile] = $mResult;
	}

	/**
		Runs the test suite.
	*/

	public function run()
	{
		$oDirectory	= new RecursiveDirectoryIterator($this->sTestsPath);
		foreach (new RecursiveIteratorIterator($oDirectory) as $sPath)
		{
			// Skip files already included
			if (in_array($sPath, get_included_files()))
				continue;

			// Skip files not ending with .php
			if (substr($sPath, -strlen(PHP_EXT)) != PHP_EXT)
				continue;

			$sClass	= uniqid('weeTest_');

			$sCode	= '
				class ' . $sClass . ' extends weeUnitTestCase
				{
					public function run()
					{
						$b = require_once("' . $sPath . '");
						return $b !== false;
					}
				}';
			eval($sCode);

			try
			{
				$oTest = new $sClass;
				$bRes = $oTest->run();

				if (!$bRes)
					continue;

				$this->addResult((string)$sPath, 'success');
			}
			catch (SkipTestException $o)
			{
				$this->addResult((string)$sPath, 'skip');
			}
			catch (Exception $o)
			{
				$this->addResult((string)$sPath, array(
					'exception' => get_class($o),
					'message' => $o->getMessage(),
				));
			}
		}
	}
}
