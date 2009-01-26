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
	Automated CLI unit testing and code coverage analysis tool.

	This unit testing tool is designed for use with the Web:Extend's framework only.
	It is meant to be very light and embedded with the framework's distribution to
	allow anyone to check if it will work correctly on their platform using a simple
	"make test". It is not meant to be used to test applications. There are much
	better tools for this purpose, like PHPUnit (http://phpunit.de).

	Unit test cases that return false value will be ignored.
	Return false if you need additional files that are not unit test cases.
*/

class weeTestSuite implements Mappable, Printable
{
	/**
		Extended data generated by the unit test suite.
	*/

	protected $aExtData = array();

	/**
		The result of the last test ran.
		Used to produce a more readable output by separating tests with different results.
	*/

	protected $mLastResult = '';

	/**
		Array containing the results of the unit test suite, after its completion.
	*/

	protected $aResults = array();

	/**
		Initialize the test suite.

		@param $sTestsPath Path to the unit test cases.
	*/

	public function __construct($sTestsPath)
	{
		!defined('WEE_CODE_COVERAGE') || function_exists('xdebug_enable')
			or burn('ConfigurationException', _WT('The XDebug PHP extension is required for code coverage analysis.'));

		if (defined('WEE_ON_WINDOWS'))
			$sTestsPath = realpath(getcwd()) . '\\' . str_replace('/', '\\', $sTestsPath);
		else
			$sTestsPath = realpath(getcwd()) . '/' . $sTestsPath;

		// Build the array containing the unit tests results

		$oDirectory = new RecursiveDirectoryIterator($sTestsPath);
		foreach (new RecursiveIteratorIterator($oDirectory) as $sPath) {
			$sPath = (string)$sPath;

			// Skip files already included
			if (in_array($sPath, get_included_files()))
				continue;

			// Skip files not ending with .php
			if (substr($sPath, -strlen(PHP_EXT)) != PHP_EXT)
				continue;

			// Skip class files
			if (substr($sPath, -strlen(CLASS_EXT)) == CLASS_EXT)
				continue;

			$this->aResults[$sPath] = 'skip';
		}
	}

	/**
		Add a result to the result array.

		Results must be either "success" or "skip" or an Exception.

		@param	$sFile			The filename of the unit test case.
		@param	$mResult		The result of the unit test case.
		@throw	DomainException	$mResult is not a valid result.
	*/

	protected function addResult($sFile, $mResult)
	{
		$mResult == 'success' or $mResult == 'skip' or is_object($mResult) and $mResult instanceof Exception
			or burn('DomainException', _WT('$mResult is not a valid result.'));

		$this->aResults[$sFile] = $mResult;

		if (!is_string($this->mLastResult) || !is_string($mResult))
			echo "--\n";
		echo $sFile, ': ';

		if ($mResult === 'success' || $mResult === 'skip')
			echo _WT($mResult), "\n";
		elseif ($mResult instanceof ErrorException) {
			echo _WT('error'), "\n",
				_WT('Message: '), $mResult->getMessage(), "\n",
				_WT('Level: '), weeException::getLevelName($mResult->getSeverity()), "\n",
				_WT('File: '), $mResult->getFile(), "\n",
				_WT('Line: '), $mResult->getLine(), "\n";
		} elseif ($mResult instanceof UnitTestException) {
			$aTrace = $mResult->getTrace();

			echo _WT('failure'), "\n", _WT('Message: '), $mResult->getMessage(), "\n",
				_WT('Line: '), array_value($aTrace[0], 'line', '?'), "\n";

			if ($mResult instanceof ComparisonTestException) {
				echo _WT('Expected: ');
				var_export($mResult->getExpected());
				echo "\n", _WT('Actual: ');
				var_export($mResult->getActual());
				echo "\n";
			}
		} else {
			echo get_class($mResult), "\n",
				_WT('Message: '), $mResult->getMessage(), "\n",
				_WT('Trace:'), "\n", $mResult->getTraceAsString(), "\n";
		}

		$this->mLastResult = $mResult;
	}

	/**
		Return an analyze of the collected information for code coverage.

		@return array The covered code.
	*/

	protected function analyzeCodeCoverage()
	{
		$aCoveredCode = array();

		foreach ($this->aExtData as $sTestFile => &$aCoveredFiles) {
			foreach ($aCoveredFiles as $sFile => $aData)
				if (is_array($aData) && array_key_exists(0, $aData) && array_key_exists(1, $aData))
					if ($aData[0] === 'weeCoveredCode') {
						foreach ($aData[1] as $sCoveredFilename => $aCoveredLines)
							foreach($aCoveredLines as $iLine => $iCovered) {
								if (!isset($aCoveredCode[$sCoveredFilename][$iLine]))
									$aCoveredCode[$sCoveredFilename][$iLine] = $iCovered;
								elseif ($iCovered > $aCoveredCode[$sCoveredFilename][$iLine])
									$aCoveredCode[$sCoveredFilename][$iLine] = $iCovered;
							}

						unset($this->aExtData[$sTestFile][$sFile]);
					}

			// Clean-up

			if (empty($aCoveredFiles))
				unset($this->aExtData[$sTestFile]);
		}

		return $aCoveredCode;
	}

	/**
		Display information about covered code, non-covered code and dead code.

		@param	$aCoveredCode	The code coverage information returned by weeTestSuite::analyzeCodeCoverage.
	*/

	protected function printCodeCoverage($aCoveredCode = array())
	{
		echo "\nCode coverage:\n";

		ksort($aCoveredCode);

		foreach ($aCoveredCode as $sFilename => $aLines) {
			echo "\n" . $sFilename;

			if (in_array(1, $aLines)) {
				echo "\nCovered: ";
				$this->printFileCoverage($aLines, 1);
			} else {
				echo "\nNO CODE COVERED";
			}

			if (in_array(-1, $aLines)) {
				echo "\nNon-covered: ";
				$this->printFileCoverage($aLines, -1);
			}

			// Dead-code handling is tricky. XDebug returns dead code for empty
			// lines with only a "}", often after a return. While it's technically
			// true, it is of no use to us and thus all these lines are removed
			// from our results (if possible). In short, first we remove them,
			// and if there's any dead code remaining, we output it.

			if (in_array(-2, $aLines)) {
				$aFile = @file($sFilename, FILE_IGNORE_NEW_LINES);
				if ($aFile !== false)
					foreach ($aLines as $iLine => $iValue)
						if (isset($aFile[$iLine - 1]) && trim($aFile[$iLine - 1]) == '}')
							unset($aLines[$iLine]);
			}

			if (in_array(-2, $aLines)) {
				echo "\nDead code: ";
				$this->printFileCoverage($aLines, -2);
			}
		}

		echo "\n";
	}

	/**
		Display information about which lines were executed or not.

		The values for $iDebugOption are:
			*  1: Covered code.
			* -1: Uncovered code.
			* -2: Dead code.

		@param	$aLines			List of lines with the coverage information associated (covered line, non-covered line or dead line).
		@param	$iDebugOption	The option indicates the kind of information to display.
	*/

	protected function printFileCoverage($aLines, $iDebugOption)
	{
		foreach ($aLines as $iCur => $iCovered) {
			if ($iCovered != $iDebugOption)
				continue;

			if (!isset($iPrev)) {
				$iPrev = $iFirst = $iCur;
				continue;
			}

			if ($iPrev + 1 != $iCur) {
				if ($iFirst != $iPrev)
					echo $iFirst . '-' . $iPrev . ';';
				else
					echo $iFirst . ';';
				$iFirst = $iCur;
			}

			$iPrev = $iCur;
		}

		if (isset($iPrev)) {
			if ($iFirst != $iPrev) 
				echo $iFirst . '-' . $iPrev . ';';
			else
				echo $iFirst . ';';
		}
	}

	/**
		Run the test suite.
	*/

	public function run()
	{
		// Run all the tests

		foreach ($this->aResults as $sPath => $mResult) {
			try {
				$oTest = new weeUnitTestCase($sPath);
				$oTest->run();

				$this->addResult($sPath, 'success');

				if ($oTest->hasExtData())
					$this->aExtData[$sPath] = $oTest->getExtData();
			} catch (SkipTestException $o) {
				$this->addResult($sPath, 'skip');
			} catch (Exception $o) {
				$this->addResult($sPath, $o);
			}
		}
	}

	/**
		Return the results array of the unit test suite.
		It is always available, even when tests have not been run yet.

		@return array Results array for all the unit test cases.
	*/

	public function toArray()
	{
		return $this->aResults;
	}

	/**
		Return the results of the unit test suite.

		@return string A report of the unit test suite after its completion.
	*/

	public function toString()
	{
		// Analyze and output the code coverage results

		if (defined('WEE_CODE_COVERAGE') && !empty($this->aExtData))
			$this->printCodeCoverage($this->analyzeCodeCoverage());

		// Output the extended data returned by the test cases

		if (!empty($this->aExtData)) {
			echo "\nExtended data:\n\n";

			foreach ($this->aExtData as $sFile => $aTestData) {
				echo $sFile . "\n";

				foreach ($aTestData as $aData) {
					echo ' ' . $aData[0] . ': ';
					if (is_array($aData[1]))
						echo str_replace(array("\n ", "\n"), '', var_export($aData[1], true));
					else
						echo $aData[1];
					echo "\n";
				}
			}
		}

		// Count the number of test failed, succeeded and skipped and output a summary

		$aCounts = @array_count_values($this->aResults);

		if (!isset($aCounts['skip']))
			$aCounts['skip'] = 0;

		$s = "\n";

		$iSkippedAndSucceededCount	= $aCounts['success'] + $aCounts['skip'];
		$iTestsCount				= count($this->aResults);

		if ($iSkippedAndSucceededCount == $iTestsCount)
			$s .= sprintf(_WT('All %d tests succeeded!'), $aCounts['success']);
		else
			$s .= sprintf(_WT('%d of %d tests failed.'), $iTestsCount - $iSkippedAndSucceededCount, $iTestsCount - $aCounts['skip']);

		if ($aCounts['skip'] != 0)
			$s .= sprintf(_WT(' (%d skipped)'), $aCounts['skip']);

		return $s . "\n";
	}
}
