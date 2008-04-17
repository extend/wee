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
	Gettext's binary MO file reader.
	For more informations, see the gettext documentation:
		http://www.gnu.org/software/gettext/manual/
*/

class weeGetTextReader
{
	/**
		Is the file compiled with little endianness?
	*/

	private $bLittleEndian = true;

	/**
		The file stream.
	*/

	private $oStream;

	/**
		The strings compiled in the binary MO file.
	*/

	private $aStrings;

	/**
		Constructs a new gettext binary MO file reader.
		@param $sFilename The name of the file to be read.
	*/

	public function __construct($sFilename)
	{
		$this->oStream = new weeFileStream($sFilename);

		switch ($this->readInt())
		{
			case (int)0x950412de:
				$this->bLittleEndian = true;
				break;

			case (int)0xde120495:
				$this->bLittleEndian = false;
				break;

			default:
				burn('UnexpectedValueException', "File '$sFilename' does not seem to be a mo file.");
		}

		// We skip the revision field, we don't need it.
		$this->oStream->seek(4, weeFileStream::SEEK_CURRENT);

		$iStringsCount				= $this->readInt();
		$iOriginalTableOffset		= $this->readInt();
		$iTranslationTableOffset	= $this->readInt();

		$aOriginalTable				= $this->readTable($iOriginalTableOffset, $iStringsCount);
		$aTranslationTable			= $this->readTable($iTranslationTableOffset, $iStringsCount);

		$aOriginalStrings			= $this->readStrings($aOriginalTable);
		$aTranslationStrings		= $this->readStrings($aTranslationTable);

		$this->aStrings				= array_combine($aOriginalStrings, $aTranslationStrings);

		unset($this->oStream);
	}

	/**
		Returns an associative array of the strings compiled in the MO file.
		@return array The strings and their translations compiled in the MO file.
	*/

	public function getStrings()
	{
		return $this->aStrings;
	}

	/**
		Reads an integer from the file stream.
		This method automatically handles endianness.
		@return int The read integer.
	*/

	private function readInt()
	{
		$s = $this->oStream->read(4);
		if ($this->bLittleEndian)
			$a = unpack('V', $s);
		else
			$a = unpack('N', $s);
		return $a[1];
	}

	/**
		Reads a table of a given size from the file stream at a given offset.
		@param	$iOffset	The offset where the table starts in the file.
		@param	$iSize		The size of the table to be read.
		@return	array		The read table.
	*/

	private function readTable($iOffset, $iSize)
	{
		$this->oStream->seek($iOffset);
		$a = array();
		for ($i = 0; $i < $iSize; ++$i)
			$a[] = $this->readTableEntry();
		return $a;
	}

	/**
		Reads a table entry from the file stream.
		The returned table entry is an associative array with 2 keys:
			- length: The length of the string.
			- offset: The offset where the string begins in the file.
		@return	array		The read table entry.
	*/

	private function readTableEntry()
	{
		$s = $this->oStream->read(8);
		if ($this->bLittleEndian)
			$a = unpack('Vlength/Voffset', $s);
		else
			$a = unpack('Nlength/Noffset', $s);
		return $a;
	}

	/**
		Reads a string of a given size from the file stream at a given offset.
		@param	$iOffset	The offset where the string begins in the file.
		@param	$iLength	The length of the string to be read.
		@retuns	string		The read string.
	*/

	private function readString($iOffset, $iLength)
	{
		if (!$iLength)
			return '';

		$this->oStream->seek($iOffset);
		return $this->oStream->read($iLength);
	}

	/**
		Reads strings referenced in a given table.
		@param	$aTable		The table referencing the strings to be read.
		@return	array		The read strings.
	*/

	private function readStrings($aTable)
	{
		$a = array();
		foreach ($aTable as $aTableEntry)
			$a[] = $this->readString($aTableEntry['offset'], $aTableEntry['length']);
		return $a;
	}
}
