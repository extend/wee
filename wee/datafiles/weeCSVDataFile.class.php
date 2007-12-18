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
	Class for manipulation of CSV files.
*/

class weeCSVDataFile
{
	/**
		Data read from or written to the CSV file.
	*/

	protected $aData = array();

	/**
		Load data from a CSV file.

		@param $sFilename	The filename of the CSV file
		@param $bAppend		Whether to keep existing data
	*/

	public function loadFromFile($sFilename, $bAppend = false)
	{
		$rFile = @fopen($sFilename, 'r');
		fire($rFile === false, 'FileNotFoundException', "Can't open file '" . $sFilename . "'.");

		if (!$bAppend)
			$this->aData = array();

		while (($a = fgetcsv($rFile)) !== false)
			$this->aData[] = $a;

		fclose($rFile);
	}

	/**
		Convert data for insertion into a database using SQL.

		@param	$oDatabase	The destination database
		@param	$sTable		The destination table in the database
		@param	$aMap		Optional array for mapping CSV indexes to database columns
		@return	array		An array of INSERT queries
	*/

	public function toSQL($oDatabase, $sTable, array $aMap = null)
	{
		fire(!($oDatabase instanceof weeDatabase), 'UnexpectedValueException',
			'$oDatabase must be an instance of weeDatabase.');
		fire(empty($sTable), 'UnexpectedValueException', '$sTable must not be empty.');

		$sSQLBegin = 'INSERT INTO ' . $sTable;

		if (!is_null($aMap))
		{
			$sSQLBegin .= '(';
			foreach ($aMap as $sName)
				$sSQLBegin .= $sName . ',';
			$sSQLBegin = substr($sSQLBegin, 0, -1) . ')';
		}

		$sSQLBegin .= ' VALUES(';

		$aReturn = array();

		foreach ($this->aData as $aRow)
		{
			$sSQL = $sSQLBegin;

			if (is_null($aMap))
			{
				foreach ($aRow as $sValue)
					$sSQL .= mb_convert_encoding($oDatabase->escape($sValue), 'UTF-8') . ',';
			}
			else
			{
				foreach ($aMap as $iKey => $sName)
					$sSQL .= mb_convert_encoding($oDatabase->escape($aRow[$iKey]), 'UTF-8') . ',';
			}

			$aReturn[] = substr($sSQL, 0, -1) . ')';
		}

		return $aReturn;
	}
}

?>
