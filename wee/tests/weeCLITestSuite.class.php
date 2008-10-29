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
	Automated unit testing.
	CLI (command line interface) version.
*/

class weeCLITestSuite extends weeTestSuite
{
	/**
		The result of the last test ran.
		Used to produce a more readable output by separating tests with different results.
	*/

	protected $mLastResult = '';

	/**
		Adds a result to the result array.

		Results must be either "success" or "skip" or an Exception.

		@param	$sFile					Filename of the unit test case.
		@param	$mResult				Result of the unit test case.
	*/

	protected function addResult($sFile, $mResult)
	{
		parent::addResult($sFile, $mResult);

		if (!is_string($this->mLastResult) || !is_string($mResult))
			echo "--\n";
		echo $sFile, ': ';

		if ($mResult === 'success' || $mResult === 'skip')
			echo _($mResult), "\n";
		elseif ($mResult instanceof ErrorTestException)
		{
			echo _('error'), "\n",
				_('Message: '), $mResult->getMessage(), "\n",
				_('Level: '), weeException::getLevelName($mResult->getCode()), "\n",
				_('File: '), $mResult->getFile(), "\n",
				_('Line: '), $mResult->getLine(), "\n";
		}
		elseif ($mResult instanceof UnitTestException)
		{
			echo _('failure'), "\n", _('Message: '), $mResult->getMessage(), "\n";
			if ($mResult instanceof ComparisonTestException)
			{
				echo _('Expected: ');
				var_export($mResult->getExpected());
				echo "\n", _('Actual: ');
				var_export($mResult->getActual());
				echo "\n";
			}
		}
		else
		{
			echo get_class($mResult), "\n",
				_('Message: '), $mResult->getMessage(), "\n",
				_('Trace:'), "\n", $mResult->getTraceAsString(), "\n";
		}

		$this->mLastResult = $mResult;
	}

	/**
		Return an analyze of the collected information for code coverage.

		@return	array	The covered code.
	*/

	protected function analyzeCodeCoverage()
	{
		$aCoveredCode = array();

		foreach ($this->aExtData as $sTestFile => &$aCoveredFiles)
		{
			foreach ($aCoveredFiles as $sFile => $aData)
				if (is_array($aData) && array_key_exists(0, $aData) && array_key_exists(1, $aData))
					if ($aData[0] === 'weeCoveredCode')
					{
						foreach ($aData[1] as $sCoveredFilename => $aCoveredLines)
							foreach($aCoveredLines as $iLine => $iCovered)
							{
								if (!isset($aCoveredCode[$sCoveredFilename][$iLine]))
									$aCoveredCode[$sCoveredFilename][$iLine] = $iCovered;
								else if ($iCovered > $aCoveredCode[$sCoveredFilename][$iLine])
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

		@param	$aCoveredCode	The code coverage information returned by weeCLITestSuite::analyzeCodeCoverage.
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
		Returns the result of the unit test suite.

		@return	string					A report of the unit test suite after its completion.
		@throw	IllegalStateException	The test suite has not been ran.
	*/

	public function toString()
	{
		$this->aResults !== null
			or burn('IllegalStateException',
				_('Please run the suite before trying to output its results.'));

		if (defined('WEE_CODE_COVERAGE') && !empty($this->aExtData))
			$this->printCodeCoverage($this->analyzeCodeCoverage());

		// Output the extended data returned by the test cases

		if (!empty($this->aExtData))
		{
			echo "\nExtended data:\n\n";

			foreach ($this->aExtData as $sFile => $aTestData)
			{
				echo $sFile . "\n";

				foreach ($aTestData as $aData)
				{
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
			$s .= sprintf(_('All %d tests succeeded!'), $aCounts['success']);
		else
			$s .= sprintf(_('%d of %d tests failed.'), $iTestsCount - $iSkippedAndSucceededCount, $iTestsCount - $aCounts['skip']);

		if ($aCounts['skip'] != 0)
			$s .= sprintf(_(' (%d skipped)'), $aCounts['skip']);

		return $s . "\n";
	}
}
