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
	Model for filesystem files.
*/

class weeFsFileModel extends weeFsModel
{
	/**
		Append content to the file.

		@param $sContents Contents to be appended to the file.
	*/

	public function appendContents($sContents)
	{
		$iSize = @file_put_contents($this->sFilename, $sContents, FILE_APPEND);
		fire($iSize === false, 'UnexpectedValueException',
			'The file contents could not be appended (disk space low? permission denied?).');

		$this->aData['size'] += $iSize;
	}

	/**
		Returns the contents of the file.

		@return string The contents of the file.
	*/

	public function getContents()
	{
		$sContents = @file_get_contents($this->sFilename);
		fire($sContents === false, 'UnexpectedValueException',
			'The file contents could not be get (permission denied?).');

		return $sContents;
	}

	/**
		Open the file for complex manipulation of its contents.

		@param $sMode Specifies the type of access you require to the file. @see http://php.net/fopen
		@return splFileObject The opened file object.
	*/

	public function open($sMode = 'r')
	{
		$oFile = new splFileObject($this->sFilename, $sMode);
		$this->bExists = true;
		return $oFile;
	}

	/**
		Set the contents of the file.
		Replace existing contents if any.

		@param $sContents The new contents of the file.
	*/

	public function setContents($sContents)
	{
		$iSize = file_put_contents($this->sFilename, $sContents);
		fire($iSize === false, 'UnexpectedValueException',
			'The file contents could not be set (disk space low? permission denied?).');

		$this->aData['size'] = $iSize;

		$this->bExists = true;
		$this->update();
	}
}
