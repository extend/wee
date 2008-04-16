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

if (!defined('ALLOW_INCLUSION')) die;

/**
	Simple class to read files.
*/

class weeFileStream
{
	/**
		Seek a position from the beginning of the file.
		@see seek()
	*/

	const SEEK_BEGINNING = SEEK_SET;

	/**
		Seek a position from the current position in the file.
		@see seek()
	*/

	const SEEK_CURRENT = SEEK_CUR;

	/**
		The file handle resource.
	*/

	private $rHandle;

	/**
		The name of the file.
	*/

	private $sFilename;

	/**
		Constructs a new file stream.
		@param $sFilename				The name of the file.
		@throw FileNotFoundException	The file does not exist.
	*/

	public function __construct($sFilename)
	{
		fire(!is_file($sFilename), 'FileNotFoundException', "File '$sFilename' does not exist.");
		$rHandle = @fopen($sFilename, 'r');

		// I don't think FileNotFoundException is the adequate exception to be thrown here.
		fire($rHandle === false, 'FileNotFoundException', "Can't open file '$sFilename'.");

		$this->rHandle		= $rHandle;
		$this->sFilename	= $sFilename;
	}

	/**
		Returns the name of the file.
		@return string The name of the file.
	*/

	public function getFilename()
	{
		return $this->sFilename;
	}

	/**
		Read a given amount of bytes from the file.
		@param	$iBytes				The amount of bytes to be read.
		@return	string				The read string.
		@throw	EndOfFileException	The end of the file has been reached.
	*/

	public function read($iBytes)
	{
		$s = fread($this->rHandle, $iBytes);
		fire(feof($this->rHandle), 'EndOfFileException',
			'Unexpected end of file while reading ' . $iBytes . " bytes from '" . $this->sFilename . "'.");

		return $s;
	}

	/**
		Seek a given position in the file.
		@param	$iPosition	The position to be sought.
		@param	$iWhence	The relative position from where to start.
		@see	SEEK_BEGINNING
		@see	SEEK_CURRENT
	*/	

	public function seek($iPosition, $iWhence = self::SEEK_BEGINNING)
	{
		$i = fseek($this->rHandle, $iPosition, $iWhence);
		fire($i == -1, 'EndOfFileException',
			"Unexpected end of file in '" . $this->sFilename . "'.");

		return $i;
	}
}
