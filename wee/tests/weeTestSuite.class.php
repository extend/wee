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

class weeTestSuite
{
	protected $sTestsPath;
	protected $aResults = array();

	public function __construct($sTestsPath)
	{
		$this->sTestsPath = $sTestsPath;

		weeAutoload::addPath($sTestsPath);
	}

	public function __toString()
	{
		$bAllSuccess = $this->run();

		if ($bAllSuccess)
			return 'success';

		//TODO
		return 'failure';
	}

	public function run()
	{
		$bAllSuccess = true;

		$oDirectory	= new RecursiveDirectoryIterator($this->sTestsPath);
		foreach (new RecursiveIteratorIterator($oDirectory) as $sPath)
		{
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
							return require("' . $sPath . '");
						}
					}';
				eval($sCode);
			}

			if (empty($sClass))
				continue; //TODO:bad file, report error

			$oTest = new $sClass;
			$bSuccess = $oTest->run();

			if ($bSuccess == null) // ignore files that return null
				continue;

			$aResults[(string)$sPath] = $bSuccess;
			$bAllSuccess &= $bSuccess;
		}

		return $bAllSuccess;
	}
}

?>
