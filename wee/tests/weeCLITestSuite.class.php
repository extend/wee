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
		Returns the result of the unit test suite.

		@return	string					A report of the unit test suite after its completion.
		@throw	IllegalStateException	The test suite has not been ran.
	*/

	public function toString()
	{
		$this->aResults !== null
			or burn('IllegalStateException',
				_('Please run the suite before trying to output its results.'));

		$aCounts = @array_count_values($this->aResults);

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
