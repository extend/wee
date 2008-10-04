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
		Add a result to the result array.
		Results can be true or an array containing exception class name and message.

		@param $sFile Filename of the unit test case.
		@param $mResult Result of the unit test case.
	*/

	protected function addResult($sFile, $mResult)
	{
		parent::addResult($sFile, $mResult);

		echo $sFile . ': ';

		if ($mResult === 'success' || $mResult === 'skip')
			echo $mResult;
		else
			echo (($mResult['exception'] == 'UnitTestException') ? 'failure' : 'error') .
				"\n    " . $mResult['message'];

		echo "\n";
	}

	/**
		Returns the result of the unit test suite.

		@return string A report of the unit test suite after its completion.
	*/

	public function toString()
	{
		$aCounts = @array_count_values($this->aResults);
		fire(!isset($aCounts['success']), 'IllegalStateException', 'Please run the suite before trying to output its results.');

		// Output the extended data returned by the test cases

		if (!empty($this->aExtData))
		{
			echo "\nExtended data:\n\n";

			foreach ($this->aExtData as $sFile => $aTestData)
				foreach ($aTestData as $aData)
				{
					echo $sFile . "\n " . $aData[0] . ': ';
					if (is_array($aData[1]))
						echo str_replace(array("\n ", "\n"), '', var_export($aData[1], true));
					else
						echo $aData[1];
					echo "\n";
				}
		}

		// Count the number of test failed, succeeded and skipped and output a summary

		if (!isset($aCounts['skip']))
			$aCounts['skip'] = 0;

		$s = "\n";

		if ($aCounts['success'] + $aCounts['skip'] == sizeof($this->aResults))
			$s .= 'All ' . $aCounts['success'] . ' tests succeeded!';
		else
			$s .= (sizeof($this->aResults) - $aCounts['success'] - $aCounts['skip']) . ' of ' . sizeof($this->aResults) . ' tests failed.';

		if ($aCounts['skip'] != 0)
			$s .= ' (' . $aCounts['skip'] . ' skipped)';

		return $s . "\n";
	}
}
