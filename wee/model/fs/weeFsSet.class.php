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
	Set of filesystem objects.
*/

class weeFsSet extends weeSet
{
	/**
		Model for directories.
	*/

	protected $sDirectoryModel = 'weeFsDirectoryModel';

	/**
		Model for links.
	*/

	protected $sLinkModel = 'weeFsLinkModel';

	/**
		Model for files.
	*/

	protected $sModel = 'weeFsFileModel';

	/**
		Returns a specific element.

		@param $sFilename Create and return a model for this parameter.
		@todo Test if the file exists and fail if it doesn't
	*/

	public function fetch($sFilename)
	{
		$aData = array('filename' => $sFilename);

		if (is_dir($sFilename))
			return new $this->sDirectoryModel($aData);
		if (is_link($sFilename))
			return new $this->sLinkModel($aData);
		return new $this->sModel($aData);
	}

	/**
		Returns all the elements in the specified path.

		@param $sPath Path to the files to return.
	*/

	public function fetchPath($sPath)
	{
		if (!is_dir($sPath))
			return array();

		$aFiles = glob($sPath . '/*');
		fire($aFiles === false, 'UnexpectedValueException',
			'An error occured while trying to retrieve a set of files.');

		foreach ($aFiles as $iKey => $sFilename)
			$aFiles[$iKey] = $this->fetch($sFilename);

		return $aFiles;
	}
}
