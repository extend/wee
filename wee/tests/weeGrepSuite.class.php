<?php

/*
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

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
	Automated static code analysis tool. Use regular expressions to find common errors.
*/

class weeGrepSuite
{
	/**
		List of files to grep.
	*/

	protected $aFiles;

	/**
		Absolute path to the files to parse.
	*/

	protected $sFilesPath;

	/**
		Absolute path to the grep rules.
	*/

	protected $sGrepFilesPath;

	/**
		List of grep rules.
	*/

	protected $aGreps;

	/**
		Initialize the grep suite.

		@param $sFilesPath Path to the files to parse.
		@param $sGrepFilesPath Path to the grep rules.
	*/

	public function __construct($sFilesPath, $sGrepFilesPath)
	{
		if (defined('WEE_ON_WINDOWS')) {
			$this->sFilesPath = realpath(getcwd()) . '\\' . str_replace('/', '\\', $sFilesPath);
			$this->sGrepFilesPath = realpath(getcwd()) . '\\' . str_replace('/', '\\', $sGrepFilesPath);
		} else {
			if ($sFilesPath[0] == '/')
				$this->sFilesPath = $sFilesPath;
			else
				$this->sFilesPath = realpath(getcwd()) . '/' . $this->sFilesPath;

			if ($sGrepFilesPath[0] == '/')
				$this->sGrepFilesPath = $sGrepFilesPath;
			else
				$this->sGrepFilesPath = realpath(getcwd()) . '/' . $sGrepFilesPath;
		}

		// Build the array containing the grep files

		$oDirectory = new RecursiveDirectoryIterator($this->sGrepFilesPath);
		foreach (new RecursiveIteratorIterator($oDirectory) as $sPath) {
			$sPath = (string)$sPath;

			// Skip files not ending with .grep
			if (substr($sPath, -strlen('.grep')) != '.grep')
				continue;

			$a = explode('/', $sPath);
			$this->aGreps[$a[count($a) - 2]][] = realpath($sPath);
		}

		ksort($this->aGreps);
		foreach ($this->aGreps as $sExt => $a)
			asort($this->aGreps[$sExt]);

		// Build the array containing the files found

		$aExtensions = array_keys($this->aGreps);
		$oDirectory = new RecursiveDirectoryIterator($this->sFilesPath);
		foreach (new RecursiveIteratorIterator($oDirectory) as $sPath) {
			$sPath = (string)$sPath;

			// Skip files already included
			if (in_array($sPath, get_included_files()))
				continue;

			// Skip files not ending with an handled extension
			$sExt = substr(strrchr($sPath, '.'), 1);
			if (!in_array($sExt, $aExtensions))
				continue;

			$this->aFiles[$sExt][] = realpath($sPath);
		}

		asort($this->aFiles);
		foreach ($this->aFiles as $sExt => $a)
			asort($this->aFiles[$sExt]);
	}

	/**
		Run the given grep rule on the given file.

		@param $sGrepFile The grep rules file.
		@param $sFilename The file to parse.
		@return bool Whether there was a match.
	*/

	public function grep($sGrepFile, $sFilename)
	{
		$aOutput = array();

		$aGreps = file($sGrepFile, FILE_IGNORE_NEW_LINES);
		$sContents = file_get_contents($sFilename);

		foreach ($aGreps as $sGrepLine) {
			$a = array();
			preg_match_all('/' . $sGrepLine . '/im', $sContents, $a, PREG_PATTERN_ORDER);
			$aOutput = array_merge($aOutput, $a[0]);
		}

		if (count($aOutput) > 0) {
			echo "\n " . str_replace(realpath($this->sFilesPath) . '/', '', $sFilename) . ':';

			$iOffset = 0;

			foreach ($aOutput as $s) {
				$i = strpos($sContents, $s, $iOffset);
				$iOffset = $i + 1;

				if ($i != 0)
					$i = 1 + substr_count($sContents, "\n", 0, $i);
				else {
					// When there's multiple patterns to match we have to restart
					// looking for the position from the start of the file.
					$i = strpos($sContents, $s);
					$iOffset = $i + 1;
					if ($i == 0)
						$i = 1;
					else
						$i = 1 + substr_count($sContents, "\n", 0, $i);
				}

				echo "\n" . sprintf('%5d ', $i) . $s;
			}

			return true;
		}

		return false;
	}

	/**
		Run the grep suite.
	*/

	public function run()
	{
		foreach ($this->aGreps as $sExt => $aTests)
			if (isset($this->aFiles[$sExt]))
				foreach ($aTests as $sGrep) {
					echo str_replace(realpath($this->sGrepFilesPath) . '/', '', $sGrep) . ': ';

					$bMatches = false;
					foreach ($this->aFiles[$sExt] as $sFile)
						$bMatches = $this->grep($sGrep, $sFile) || $bMatches;

					if ($bMatches) {
						echo "\n";
						readfile(substr($sGrep, 0, strrpos($sGrep, '.')) . '.about');
						echo "\n";
					} else
						echo "ok\n";
				}
	}
}
