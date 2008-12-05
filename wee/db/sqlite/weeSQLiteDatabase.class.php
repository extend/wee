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
	SQLite database driver.
*/

class weeSQLiteDatabase extends weeDatabase
{
	/**
		The database object.
	*/

	protected $oDb;

	/**
		The last error returned by SQLite.
	*/

	protected $sLastError;

	/**
		Initialises a new sqlite database.

		This driver accepts the following parameters:
		 - create:	Whether to create the database file if it does not exist (defaults to false).
		 - file:	The filename of the SQLite database (mandatory).

		@param	$aParams					The parameters of the driver.
		@throw	ConfigurationException		The SQLite PHP extension is missing.
		@throw	InvalidArgumentException	The `file` parameter is missing.
		@throw	UnexpectedValueException	The database file does not exist and the `create` parameter does not evaluate to true.
		@throw	DatabaseException			The database could not be opened.
	*/

	public function __construct($aParams = array())
	{
		function_exists('sqlite_factory')
			or burn('ConfigurationException',
				_WT('The SQLite PHP extension is required by the SQLite database driver.'));

		isset($aParams['file'])
			or burn('InvalidArgumentException',
				_WT('The `file` argument is missing.'));

		$aParams += array(
			'create' => false
		);

		$aParams['create'] or file_exists($aParams['file'])
			or burn('UnexpectedValueException',
				_WT('The database file does not exist and the `create` parameter does not evaluate to true.'));

		$oDb = sqlite_factory($aParams['file'], 0666, $this->sLastError);
		$oDb !== null
			or burn('DatabaseException',
				_WT('SQLite failed to open the database with the following error:') . "\n" . $this->sLastError);

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
		$this->sLastError = null;
		$m = @$this->oDb->query($sQuery, SQLITE_ASSOC, $this->sLastError);

		if ($m === false)
		{
			if ($this->sLastError === null)
				$this->sLastError = sqlite_error_string($this->oDb->lastError());

			burn('DatabaseException',
				_WT('SQLite failed to execute the query with the following error:') . "\n" . $this->sLastError);
		}

		if ($m->numFields())
			return new weeSQLiteResult($m);
	}

	/**
		Escapes a given identifier for safe concatenation in an SQL query.

		@param	$sValue						The identifier to escape.
		@return	string						The escaped identifier, wrapped around adequate quotes.
		@throw	InvalidArgumentException	The given identifier is empty.
		@todo								More tests, may have to read the source of SQLite 2 to know all the identifier constraints.
	*/

	public function escapeIdent($sValue)
	{
		strlen($sValue) > 0
			or burn('InvalidArgumentException',
				_WT('The given identifier is empty.'));

		return '"' . str_replace('"', '""', $sValue) . '"';
	}

	/**
		Gets the last error the database returned.

		@return	mixed	The last error returned by the database or null.
	*/

	public function getLastError()
	{
		$this->sLastError !== null
			or burn('IllegalStateException',
				_WT('SQLite did not returned an error during the last operation.'));

		return $this->sLastError;
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
		Returns the primary key index value of the last inserted row.

		@param	$sName	Unused in this database driver.
		@return	int		The primary key index value.
	*/

	public function getPKId($sName = null)
	{
		return $this->oDb->lastInsertRowid();
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.

		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return int		The number of affected rows in the last query.
	*/

	public function numAffectedRows()
	{
		return $this->oDb->changes();
	}
}
