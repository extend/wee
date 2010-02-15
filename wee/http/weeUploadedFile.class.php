<?php

/**
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
	Handle files uploaded using forms.
*/

class weeUploadedFile
{
	/**
		Original filename, before it was uploaded.
	*/

	public $sSrcName;

	/**
		Temporary filename, after it was uploaded but before being moved.
	*/

	public $sTmpName;

	/**
		MIME type, if available.
		Don't rely on it for determining the MIME type of the file.
	*/

	public $sMimeType;

	/**
		Size of the file, in bytes, as reported by PHP.
	*/

	public $iSize;

	/**
		The error code specified by PHP for this file.

		@see http://fr.php.net/manual/en/features.file-upload.errors.php
	*/

	public $iErrorCode;

	/**
		Initialize the file class.

		@param	$sSrcName	The original name of the file on the client machine.
		@param	$sTmpName	The temporary filename of the file in which the uploaded file was stored on the server.
		@param	$sMimeType	The mime type of the file, if available.
		@param	$iSize		The size, in bytes, of the uploaded file.
		@param	$iErrorCode	The error code associated with this file upload.
	*/

	public function __construct($sSrcName, $sTmpName, $sMimeType, $iSize, $iErrorCode)
	{
		$this->sSrcName		= $sSrcName;
		$this->sTmpName		= $sTmpName;
		$this->sMimeType	= $sMimeType;
		$this->iSize		= $iSize;
		$this->iErrorCode	= $iErrorCode;
	}

	/**
		Tests if given destination file exists.
		Use it to check before moving a file if you won't ecrase another.

		@param	$sDestination	The path of the destination file.
		@param	$sNewFilename	The destination filename. If null, it is the same filename as the source file.
		@return	bool			True if the file exists, false otherwise.
	*/

	public function fileExists($sDestination, $sNewFilename = null)
	{
		if (empty($sNewFilename))
			$sNewFilename	= $this->getFilename();

		return file_exists($sDestination . '/' . $sNewFilename);
	}

	/**
		Returns the error string for this file error.
		Do not call it if the file is good (isOK returns true).

		@return string The error message for this file error code.
	*/

	public function getError()
	{
		$this->iErrorCode == UPLOAD_ERR_OK and burn('IllegalStateException',
			_WT('There was no error while uploading files. Please call weeUploadedFile::getError only if weeUploadedFile::isOK returns true.'));

		$aErrorMessages = array(
			UPLOAD_ERR_INI_SIZE		=> _WT('The uploaded file size exceeds the upload_max_filesize directive in php.ini.'),
			UPLOAD_ERR_FORM_SIZE	=> _WT('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.'),
			UPLOAD_ERR_PARTIAL		=> _WT('The file upload was not completed.'),
			UPLOAD_ERR_NO_FILE		=> _WT('No file was uploaded.'),
			UPLOAD_ERR_NO_TMP_DIR	=> _WT('Missing a temporary folder.'),
			UPLOAD_ERR_CANT_WRITE	=> _WT('Failed to write file to disk.'),
		);

		if (!empty($aErrorMessages[$this->iErrorCode]))
			return $aErrorMessages[$this->iErrorCode];

		return _WT('Unknown error.');
	}

	/**
		Returns the extension of the file.

		@return string The file's extension.
	*/

	public function getExt()
	{
		$m	= strrchr($this->getFilename(), '.');
		if ($m === false)
			return null;
		return substr($m, 1);
	}

	/**
		Returns the filename without including the path.

		@return string The file's filename.
	*/

	public function getFilename()
	{
		return basename($this->sSrcName);
	}

	/**
		Tests if the file is correctly uploaded.

		@return bool True if the file is OK, false otherwise.
	*/

	public function isOK()
	{
		return $this->iErrorCode == UPLOAD_ERR_OK;
	}

	/**
		Moves the file to the specified folder, and rename it if needed.

		@param	$sDestination	The path of the destination file.
		@param	$sNewFilename	The destination filename. If null, it is the same filename as the source file.
	*/

	public function moveTo($sDestination, $sNewFilename = null)
	{
		clearstatcache();
		is_dir($sDestination) or burn('InvalidArgumentException',
			sprintf(_WT('Destination "%s" is not a directory.'), $sDestination));
		is_uploaded_file($this->sTmpName) or burn('UnexpectedValueException',
			sprintf(_WT('PHP reported that "%s" is not an uploaded file.'), $this->sTmpName));

		if (empty($sNewFilename))
			$sNewFilename = $this->getFilename();

		move_uploaded_file($this->sTmpName, $sDestination . '/' . $sNewFilename);
	}
}
