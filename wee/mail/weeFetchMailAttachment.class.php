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
	Handles fetched email attachments.
*/

class weeFetchMailAttachment
{
	/**
		Attachment file data.
	*/

	protected $sData;

	/**
		Attachment filename.
	*/

	protected $sFilename;

	/**
		Initialize the attachment object.

		@param $sFilename Attachment filename.
		@param $sData Attachment file data.
	*/

	public function __construct($sFilename, $sData)
	{
		$this->sFilename	= $sFilename;
		$this->sData		= $sData;
	}

	/**
		@return string The attachment file data.
	*/

	public function getData()
	{
		return $this->sData;
	}

	/**
		@return string The attachment filename.
	*/

	public function getFilename()
	{
		return $this->sFilename;
	}

	/**
		Save the file to the given directory.

		@param	$sDestination	The path of the destination file.
		@param	$sNewFilename	The destination filename. If null, it is the same filename as given in the email.
	*/

	public function saveTo($sDestination, $sNewFilename = null)
	{
		if (is_null($sNewFilename))
			$sNewFilename = $this->sFilename;

		file_put_contents($sDestination . '/' . $sNewFilename, $this->sData);
	}
}
