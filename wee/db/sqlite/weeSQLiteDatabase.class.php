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
	SQLite 2 database driver.
*/

class weeSQLiteDatabase extends weeDatabase
{
	/**
		The database object.
	*/

	protected $oDb;

	/**
		The name of the underlying DBMS (sqlite2).
	*/

	protected $sDBMS = 'sqlite2';

	/**
		The number of affected rows by the last query.
	*/

	protected $iNumAffectedRows;

	/**
		Initialises a new sqlite database.

		This driver accepts the following parameters:
		 * create:	Whether to create the database file if it does not exist (defaults to false).
		 * file:	The filename of the SQLite database (mandatory).

		@param	$aParams					The parameters of the driver.
		@throw	ConfigurationException		The SQLite PHP extension is missing.
		@throw	InvalidArgumentException	Parameter "file" is missing.
		@throw	FileNotFoundException		The database file does not exist and the parameter "create" does not evaluate to true.
		@throw	DatabaseException			Failed to connect to the database.
	*/

	public function __construct($aParams = array())
	{
		function_exists('sqlite_factory') or burn('ConfigurationException',
			sprintf(_WT('The %s PHP extension is required by this database driver.'), 'SQLite'));

		isset($aParams['file']) or burn('InvalidArgumentException',
			sprintf(_WT('Parameter "%s" is missing.'), 'file'));

		$aParams += array(
			'create' => false
		);

		$aParams['create'] or file_exists($aParams['file']) or burn('FileNotFoundException',
			_WT('The database file does not exist and the parameter "create" does not evaluate to true.'));

		$oDb = sqlite_factory($aParams['file'], 0666, $sLastError);
		$oDb !== null or burn('DatabaseException',
			_WT('Failed to connect to the database with the following error:') . "\n" . $sLastError);

		$this->oDb = $oDb;
	}

	/**
		Does the sqlite-dependent logic of the escape operation.

		@param	$mValue	The value to escape.
		@return	string	The escaped value.
	*/

	protected function doEscape($mValue)
	{
		return "'" . sqlite_escape_string($mValue) . "'";
	}

	/**
		Executes an SQL query.

		@param	$sQuery				The query to execute.
		@return	weeSQLiteResult		For queries that return rows, the result object.
		@throw	DatabaseException	SQLite failed to execute the query.
	*/

	protected function doQuery($sQuery)
	{
		// SQLiteDatabase::query triggers a warning when the query could not be executed.
		$m = @$this->oDb->query($sQuery, SQLITE_ASSOC, $sLastError);

		if ($m === false)
		{
			if ($sLastError === null)
				$sLastError = sqlite_error_string($this->oDb->lastError());
			burn('DatabaseException', _WT('Failed to execute the query with the following error:') . "\n" . $sLastError);
		}

		$this->iNumAffectedRows = $this->oDb->changes();
		if ($m->numFields())
			return new weeSQLiteResult($m);
	}

	/**
		Escape a given identifier for safe concatenation in an SQL query.

		Be careful when using escaped identifiers in the field list of a SELECT query as
		they will be used as the keys of the result set.

		@param	$sValue	The identifier to escape.
		@return	string	The escaped identifier, wrapped around adequate quotes.
	*/

	public function escapeIdent($sValue)
	{
		return '[' . str_replace(']', ']]', $sValue) . ']';
	}

	/**
		Returns the name of the dbmeta class associated with this driver.

		@param	mixed	The name of the dbmeta class or null if the driver does not support dbmeta.
	*/

	public function getMetaClass()
	{
		return 'weeSQLiteDbMeta';
	}

	/**
		Returns the last sequence value generated by the database in this session.

		In SQLite, it's the last value generated for an INTEGER PRIMARY KEY column by an INSERT query.

		@param	$sName					Unused in this database driver.
		@return	int						The last value generated.
		@throw	IllegalStateException	No sequence value has been generated yet by the database in this session.
	*/

	public function getPKId($sName = null)
	{
		$i = $this->oDb->lastInsertRowid();
		$i != 0 or burn('IllegalStateException',
			_WT('No sequence value has been generated yet by the database in this session.'));
		return $i;
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

	/**
		Prepares an SQL query statement.

		@param	$sQuery				The query string.
		@return	weeSQLiteStatement	The prepared statement.
	*/

	public function prepare($sQuery)
	{
		return new weeSQLiteStatement($this, $sQuery, $this->oDb);
	}
}
