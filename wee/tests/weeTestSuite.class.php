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

/**
	Automated unit testing.

	Unit test cases can return three values: true, false, or null.
	Unit test cases that return null value will be ignored.
	Use it if you need additional files that are not unit test case.
*/

class weeTestSuite implements Printable
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
		Initialize the test suite.
		Sets the path to the unit test cases, and add this path to the autoload paths.

		@param $sTestsPath Path to the unit test cases.
	*/

	public function __construct($sTestsPath)
	{
		$this->sTestsPath = $_SERVER['PWD'] . '/' . $sTestsPath;

		weeAutoload::addPath($sTestsPath);
	}

	/**
		Runs the test suite.
	*/

	public function run()
	{
		$bAllSuccess = true;

		$oDirectory	= new RecursiveDirectoryIterator($this->sTestsPath);
		foreach (new RecursiveIteratorIterator($oDirectory) as $sPath)
		{
			if (in_array($sPath, get_included_files()))
				continue;

			$sClass = null;

			if (substr($sPath, -strlen(CLASS_EXT)) == CLASS_EXT)
				$sClass	= substr(strrchr($sPath, '/'), 1, -strlen(CLASS_EXT));
			elseif (substr($sPath, -strlen(PHP_EXT)) == PHP_EXT)
			{
				$sClass	= uniqid('weeTest_');

				$sCode	= '
					class ' . $sClass . ' extends weeUnitTestCase
					{
						public function run()
						{
							return require_once("' . $sPath . '");
						}
					}';
				eval($sCode);
			}

			if (empty($sClass))
				continue; //TODO:bad file, report error

			try
			{
				$oTest = new $sClass;
				$bSuccess = $oTest->run();
			}
			catch (Exception $o)
			{
				$bSuccess = false;
			}

			if (is_null($bSuccess)) // ignore files that return null
				continue;

			//TODO:output this only if we are in CLI mode
			echo $sPath . '... ' . ($bSuccess ? 'ok' : 'error') . "\r\n";

			$this->aResults[(string)$sPath] = (int)$bSuccess;
			$bAllSuccess &= $bSuccess;
		}

		return $bAllSuccess;
	}

	/**
		Returns the result of the unit test suite.

		@return A simple report of the unit test suite after its completion.
	*/

	public function toString()
	{
		//TODO:this doesn't feels right here...
		$bAllSuccess = $this->run();

		$s = '';

		if ($bAllSuccess)
			return 'Results of the test suite: all ' . sizeof($this->aResults) . " unit test cases succeeded.\r\n";

		$aCount = array_count_values($this->aResults);

		return 'Results of the test suite: ERROR: ' . $aCount[0] . ' of ' . sizeof($this->aResults) . " unit test cases failed.\r\n";
	}
}

?>
