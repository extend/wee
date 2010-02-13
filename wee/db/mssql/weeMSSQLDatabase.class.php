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
	MS SQL database driver.
*/

class weeMSSQLDatabase extends weeDatabase
{
	/**
		Link resource for this database connection.
	*/

	protected $rLink;

	/**
		The name of the underlying DBMS (mssql).
	*/

	protected $sDBMS = 'mssql';

	/**
		Number of affected rows for the previous query.
		Stocked here to prevent errors if getPKId is called.
	*/

	protected $iNumAffectedRows;

	/**
		Initialises a new mssql database.

		This database driver accepts the following parameters:
		 * host:		The server of the database as specified by mssql_connect.
		 * user:		The username.
		 * password:	The password.
		 * dbname:		The name of the database.

		@param	$aParams	The parameters of the database.
		@throw	ConfigurationException	The MSSQL PHP extension is missing.
		@throw	DatabaseException		The connection failed.
	*/

	public function __construct($aParams = array())
	{
		function_exists('mssql_connect') or burn('ConfigurationException',
			sprintf(_WT('The %s PHP extension is required by this database driver.'), 'MSSQL'));

		// mssql_connect triggers a warning if the connection failed.
		// Don't use mssql_get_last_message here as it does not always return
		// something useful on connection failure.
		$this->rLink = mssql_connect(array_value($aParams, 'host'), array_value($aParams, 'user'), array_value($aParams, 'password'), true);
		$this->rLink !== false or burn('DatabaseException',
			_WT('Failed to connect to database with the following message:') . "\n" . array_value(error_get_last(), 'message'));
	
		if (isset($aParams['dbname']))
			$this->selectDb($aParams['dbname']);
	}

	/**
		Does the mssql-dependent logic of the escape operation.

		@param	$mValue	The value to escape.
		@return	string	The escaped value.
	*/

	public function doEscape($mValue)
	{
		// Bool isn't supported directly, cast to int
		if (is_bool($mValue))
			$mValue = (int)$mValue;
		return "'" . str_replace("'", "''", $mValue) . "'";
	}

	/**
		Execute an SQL query.

		@param	$sQueryString	The query string
		@return	weePgSQLResult	Only with SELECT queries: an object for results handling
	*/

	protected function doQuery($sQueryString)
	{
		// mssql_query triggers a warning when the query could not be executed.
		$m = @mssql_query($sQueryString, $this->rLink);
		$m === false and burn('DatabaseException', _WT('Failed to execute the given query with the following message:')
			. "\n" . mssql_get_last_message());

		// Get it now since it can be wrong if numAffectedRows is called after getPKId
		$this->iNumAffectedRows = mssql_rows_affected($this->rLink);

		if (is_resource($m))
			return new weeMSSQLResult($m);
	}

	/**
		Escape the given identifier for safe concatenation in an SQL query.

		@param	$sValue						The identifier to escape.
		@return	string						The escaped identifier.
		@throw	InvalidArgumentException	The given value is not a valid mssql identifier.
	*/

	public function escapeIdent($sValue)
	{
		if ($sValue instanceof Printable)
			$sValue = $sValue->toString();

		$i = strlen($sValue);
		$i != 0 && $i < 129 or burn('InvalidArgumentException',
			_WT('The given value is not a valid identifier.'));

		return '[' . str_replace(']', ']]', $sValue) . ']';
	}

	/**
		Returns the name of the pgsql dbmeta class.

		@param	mixed	The name of the mssql dbmeta class.
	*/

	public function getMetaClass()
	{
		return 'weeMSSQLDbMeta';
	}

	/**
		Returns the last sequence value generated by the database in this session.

		@param	$sName					Unused in this database driver.
		@return	int						The last value generated.
		@throw	IllegalStateException	No value has been generated yet for the given sequence in this session.
	*/

	public function getPKId($sName = null)
	{
		$r = mssql_query('SELECT SCOPE_IDENTITY()', $this->rLink, 1);
		$m = mssql_result($r, 0, 0);
		$m !== null or burn('IllegalStateException',
			_WT('No sequence value has been generated yet by the database in this session.'));
		return $m;
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return int	The number of affected rows in the last query.
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}

	/**
		Prepare an SQL query statement.

		@param	$sQuery				The query to prepare.
		@return	weeMSSQLStatement	The prepared statement.
	*/

	public function prepare($sQuery)
	{
		return new weeMSSQLStatement($this, $this->rLink, $sQuery);
	}

	/**
		Changes database without reconnecting.
		The new database must be on the same host of the previous.

		@param	$sDatabase			The database to use.
		@throw	DatabaseException	Failed to select the database.
	*/

	public function selectDb($sDatabase)
	{
		// mssql_select_db triggers a warning when the selection failed.
		@mssql_select_db($sDatabase, $this->rLink) or burn('DatabaseException',
			sprintf(_WT('Failed to select the database "%s" with the following message:'), $sDatabase)
				. "\n" . mssql_get_last_message());
	}
}
