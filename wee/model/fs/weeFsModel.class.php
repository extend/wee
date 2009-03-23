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
	Base class for defining a model for filesystem objects.

	If the file exists the constructor will also make available
	the following keys (use $this[$sKey] to access them):
	* filename:		path + filename [2]
	* name:			file name (everything after / in filename)
	* extension:	file extension (if any)
	* dev:			device number
	* ino:			inode number
	* mode:			inode protection mode
	* nlink:		number of links
	* uid:			userid of owner
	* gid:			groupid of owner
	* rdev:			device type, if inode device [1]
	* size:			size in bytes
	* atime:		time of last access (Unix timestamp) [2]
	* mtime:		time of last modification (Unix timestamp) [2]
	* ctime:		time of last inode change (Unix timestamp)
	* blksize:		blocksize of filesystem IO [1]
	* blocks:		number of blocks allocated [1]

		* [1] not available on Windows (returns -1)
		* [2] can be modified and applied using save()

	You can rewrite everything, however your changes may be overwritten
	by using some of the methods.

	@todo Normalise paths.
*/

abstract class weeFsModel extends weeModel
{
	/**
		Whether the file exists on the filesystem.
	*/

	protected $bExists;

	/**
		Current filename of the file for this model.

		We must keep a copy here because changing the filename in $aData
		will result in a file move, but that move will not happen before
		the user apply the changes using save.
	*/

	protected $sFilename;

	/**
		Creates a new instance of this model and fetch related data
		using the 'filename' key from the array parameter.

		@param $aData Data to be set at initialization.
	*/

	public function __construct($aData = array())
	{
		empty($aData['filename']) and burn('InvalidArgumentException',
			_WT('You must specify the filename in $aData[\'filename\'].'));

		// We want to handle everything UNIX-like
		$aData['filename'] = str_replace('\\', '/', $aData['filename']);
		$this->sFilename = $aData['filename'];

		$this->aData	= $aData;
		$this->bExists	= file_exists($aData['filename']);

		if ($this->bExists)
			$this->update();
		$this->updateName();
	}

	/**
		Delete the file from the filesystem.
	*/

	public function delete()
	{
		unlink($this->sFilename);
		$this->bExists = false;
		// TODO: unset stats?
	}

	/**
		Returns whether the file exist on the filesystem.

		@return bool Whether the file exist on the filesystem.
	*/

	public function exists()
	{
		return $this->bExists;
	}

	/**
		Returns whether the file is readable.

		@return bool Whether the file is readable.
	*/

	public function isReadable()
	{
		return is_readable($this->sFilename);
	}

	/**
		Returns whether the file is writable.

		@return bool Whether the file is writable.
	*/

	public function isWritable()
	{
		return is_writable($this->sFilename);
	}

	/**
		Make a link to this file.

		@param $sLinkFilename Filename of the link to this file.
	*/

	public function makeLink($sLinkFilename)
	{
		defined('WEE_ON_WINDOWS') and burn('ConfigurationException',
			_WT('This method is not available on Windows.'));

		$this->bExists or burn('IllegalStateException',
			_WT('The file could not be linked because it does not exist.'));

		symlink($this->sFilename, $sLinkFilename);
	}

	/**
		Move the file.

		@param $sNewFilename The new filename of the file.
	*/

	public function moveTo($sNewFilename)
	{
		$bResult = rename($this->sFilename, $sNewFilename);
		$bResult or burn('UnexpectedValueException',
			sprintf(_WT('Could not rename the file "%s" to "%s".'), $this->sFilename, $sNewFilename));

		$this->sFilename = $sNewFilename;
		$this->aData['filename'] = $sNewFilename;

		$this->updateName();
	}

	/**
		Saves all the changes made to this object.

		Only move the file if filename changed, and do a touch on it
		with the modification and access times.

		@todo Save more stuff?
		@todo Save should write a new directory/link and not a plain file if the file doesn't exist and it is a directory/link...
	*/

	public function save()
	{
		if ($this->bExists)
			touch($this->sFilename, $this->aData['mtime'], $this->aData['atime']);
		else
			touch($this->sFilename);

		$this->bExists = true;

		if ($this->sFilename != $this->aData['filename'])
			$this->moveTo($this->aData['filename']);

		$this->update();
		$this->updateName();
	}

	/**
		Update our data array with up to date stats.
	*/

	public function update()
	{
		clearstatcache();

		$aStat = lstat($this->sFilename);
		for ($i = 0; $i <= 12; $i++)
			unset($aStat[$i]);

		$this->aData = $aStat + $this->aData;
	}

	/**
		Update the extension of the file.
	*/

	protected function updateName()
	{
		if (strpos($this->sFilename, '/') !== false)
			$this->aData['name'] = substr(strrchr($this->sFilename, '/'), 1);
		else
			$this->aData['name'] = $this->sFilename;

		$aExplode = explode('.', $this->aData['name']);
		$this->aData['extension'] = (count($aExplode) < 2) ? '' : $aExplode[count($aExplode) - 1];
	}
}
