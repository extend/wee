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
	A dummy prepared statement object for the sqlite driver.

	Instances of this class are returned by weeSQLiteDatabase's prepare method and
	should not be instantiated manually.
*/

class weeSQLiteStatement extends weeDatabaseDummyStatement
{
	/**
		The SQLite database.
	*/

	protected $oSQLiteDb;

	/**
		The number of affected rows by the last execution of the statement.
	*/

	protected $iNumAffectedRows;

	/**
		Initialises a new sqlite dummy prepared statement with a given query.

		@param	$oDb	The database to use.
		@param	$sQuery	The query.
	*/

	public function __construct(weeSQLiteDatabase $oDb, $sQuery, SQLiteDatabase $oSQLiteDb)
	{
		parent::__construct($oDb, $sQuery);
		$this->oSQLiteDb = $oSQLiteDb;
	}

	/**
		Does the sqlite-dependent work of the execute method.

		@param	$sQuery			The query to execute.
		@return	weeSQLiteResult	A result set for SELECT queries.
	*/

	protected function doQuery($sQuery)
	{
		// SQLiteDatabase::query triggers a warning when the query could not be executed.
		$m = @$this->oSQLiteDb->query($sQuery, SQLITE_ASSOC, $sLastError);

		if ($m === false)
		{
			if ($sLastError === null)
				$sLastError = sqlite_error_string($this->oSQLiteDb->lastError());
			burn('DatabaseException', _WT('Failed to execute the query with the following error:') . "\n" . $sLastError);
		}

		$this->iNumAffectedRows = $this->oSQLiteDb->changes();
		if ($m->numFields())
			return new weeSQLiteResult($m);
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.

		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		Please note that SQLite always return 0 for an unconditional DELETE statement (e.g.
		DELETE FROM tablename), if you need the number of deleted rows, you should execute
		DELETE FROM tablename WHERE 1.

		@return int		The number of affected rows in the last query.
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}
}
