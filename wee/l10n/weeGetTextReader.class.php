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
	Gettext's binary MO file reader.
	For more informations, see the gettext documentation:
		http://www.gnu.org/software/gettext/manual/
*/

class weeGetTextReader
{
	/**
		The file stream.
	*/

	protected $rHandle;

	/**
		Is the file compiled with little endianness?
	*/

	protected $bLittleEndian = true;

	/**
		The strings compiled in the binary MO file.
	*/

	protected $aStrings;

	/**
		Initialises a new gettext binary MO file reader.

		@param	$sFilename					The name of the file to be read.
		@throw	FileNotFoundException		The given file does not exist.
		@throw	NotPermittedException		The given file could not be opened.
		@throw	UnexpectedValueException	The given file does not seem to be a MO file.
	*/

	public function __construct($sFilename)
	{
		$this->rHandle = fopen($sFilename, 'r');

		switch ($this->readInt())
		{
			case (int)0x950412de:
				$this->bLittleEndian = true;
				break;

			case (int)0xde120495:
				$this->bLittleEndian = false;
				break;

			default:
				burn('UnexpectedValueException',
					sprintf(_WT('File "%s" does not seem to be a MO file.'), $sFilename));
		}

		// We skip the revision field, we don't need it.
		$this->seek(4, SEEK_CUR);

		$iStringsCount				= $this->readInt();
		$iOriginalTableOffset		= $this->readInt();
		$iTranslationTableOffset	= $this->readInt();

		$aOriginalTable				= $this->readTable($iOriginalTableOffset, $iStringsCount);
		$aTranslationTable			= $this->readTable($iTranslationTableOffset, $iStringsCount);

		$aOriginalStrings			= $this->readStrings($aOriginalTable);
		$aTranslationStrings		= $this->readStrings($aTranslationTable);

		$this->aStrings				= array_combine($aOriginalStrings, $aTranslationStrings);
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
		Reads a given amount of bytes from the file.

		@param	$iBytes				The amount of bytes to be read.
		@return	string				The read string.
		@throw	EndOfFileException	The end of the file has been reached.
	*/

	protected function read($iBytes)
	{
		$s = fread($this->rHandle, $iBytes);
		!feof($this->rHandle) or burn('EndOfFileException',
			sprintf(_WT('Unexpected end of file while reading %d bytes from "%s".'), $iBytes, $this->sFilename));

		return $s;
	}

	/**
		Reads an integer from the file stream.

		This method automatically handles endianness.

		@return	int	The read integer.
	*/

	protected function readInt()
	{
		$s = $this->read(4);
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

	protected function readTable($iOffset, $iSize)
	{
		$this->seek($iOffset);
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

	protected function readTableEntry()
	{
		$s = $this->read(8);
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
		@return	string		The read string.
	*/

	protected function readString($iOffset, $iLength)
	{
		if (!$iLength)
			return '';

		$this->seek($iOffset);
		return $this->read($iLength);
	}

	/**
		Reads strings referenced in a given table.

		@param	$aTable		The table referencing the strings to be read.
		@return	array		The read strings.
	*/

	protected function readStrings($aTable)
	{
		$a = array();
		foreach ($aTable as $aTableEntry)
			$a[] = $this->readString($aTableEntry['offset'], $aTableEntry['length']);
		return $a;
	}

	/**
		Seeks a given position in the file.

		@param	$iPosition			The position to be sought.
		@param	$iWhence			The relative position from where to start.
		@throw	EndOfFileException	Unexpected end of file while seeking through the file.
		@see	fseek()
	*/	

	protected function seek($iPosition, $iWhence = SEEK_SET)
	{
		$i = fseek($this->rHandle, $iPosition, $iWhence);
		$i != -1 or burn('EndOfFileException',
			sprintf(_WT('Unexpected end of file while seeking through "%s".'), $this->sFilename));

		return $i;
	}
}
