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
	A dummy prepared statement object for the mssql driver.

	Instances of this class are returned by weeMSSQLDatabase's prepare method and
	should not be instantiated manually.
*/

class weeMSSQLStatement extends weeDatabaseDummyStatement
{
	/**
		The MSSQL database link.
	*/

	protected $rLink;

	/**
		The number of affected rows by the last execution of the statement.
	*/

	protected $iNumAffectedRows;

	/**
		Initialises a new sqlite dummy prepared statement with a given query.

		@param	$oDb	The database to use.
		@param	$sQuery	The query.
	*/

	public function __construct(weeMSSQLDatabase $oDb, $rLink, $sQuery)
	{
		is_resource($rLink) && get_resource_type($rLink) == 'mssql link' or burn('InvalidArgumentException',
			sprintf(_WT('The given variable must be a resource of type "%s".'), 'mssql link'));

		parent::__construct($oDb, $sQuery);
		$this->rLink = $rLink;
	}

	/**
		Does the mssql-dependent work of the execute method.

		@param	$sQuery			The query to execute.
		@return	weeSQLiteResult	A result set for SELECT queries.
	*/

	protected function doQuery($sQuery)
	{
		// mssql_query triggers a warning when the query could not be executed.
		$m = @mssql_query($sQuery, $this->rLink);
		$m === false and burn('DatabaseException',
			sprintf(_WT("Failed to execute the query with the following error:\n%s"), mssql_get_last_message()));

		// Get it now since it can be wrong if numAffectedRows is called after getPKId
		$this->iNumAffectedRows = mssql_rows_affected($this->rLink);

		if ($m !== true)
			return new weeMSSQLResult($m);
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
