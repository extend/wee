<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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
	Class for SQLite query results handling.

	Instances of this class are returned by weeSQLiteDatabase's query method and
	should not be instantiated manually.
*/

class weeSQLiteResult extends weeDatabaseResult
{
	/**
		The sqlite result object.
	*/

	protected $oResult;

	/**
		Initialises a new sqlite result set.

		@param	$oResult	The sqlite result object.
	*/

	public function __construct(SQLiteResult $oResult)
	{
		$this->oResult = $oResult;
	}

	/**
		Return the number of results returned by the query.

		@return	int			The number of results.
	*/

	public function count()
	{
		return $this->oResult->numRows();
	}

	/**
		Fetches the data of the next row of the result set.

		@return	mixed		An array containing the data of the next row or false if there is no current row.
	*/

	protected function doFetch()
	{
		return $this->oResult->fetch(SQLITE_ASSOC);
	}

	/**
		Rewinds the result set to its first row.
	*/

	protected function doRewind()
	{
		$this->oResult->rewind();
	}

	/**
		Fetches all the rows of the result set.

		The return value type can differ depending on the row class.
		The row class can be changed using the rowClass method.

		This method should not be used when iterating over the rows of the result set
		through the Iterator interface.

		@return	array(mixed)	An array of arrays or instances of weeDatabaseRow.
	*/

	public function fetchAll()
	{
		if ($this->sRowClass !== null)
			return parent::fetchAll();

		$a = $this->oResult->fetchAll(SQLITE_ASSOC);
		if ($this->bMustEncodeData)
			return weeOutput::encodeArray($a);
		return $a;
	}
}
