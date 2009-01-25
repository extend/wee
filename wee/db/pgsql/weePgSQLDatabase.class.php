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
	PostgreSQL database driver.
*/

class weePgSQLDatabase extends weeDatabase
{
	/**
		Link resource for this database connection.
	*/

	protected $rLink;

	/**
		Number of affected rows for the previous query.
		Stocked here to prevent errors if getPKId is called.
	*/

	protected $iNumAffectedRows;

	/**
		Initialises a new pgsql database.

		This database driver accepts the same parameters as the ones allowed in the connection string
		passed to pg_connect.

		@param	$aParams	The parameters of the database.
		@see	http://php.net/pg_connect
	*/

	public function __construct($aParams = array())
	{
		function_exists('pg_connect') or burn('ConfigurationException',
			sprintf(_WT('The %s PHP extension is required by this database driver.'), 'PostgreSQL'));

		//TODO:maybe quote & escape values...
		$sConnection = null;
		foreach ($aParams as $sKey => $sValue)
			$sConnection .= $sKey . '=' . $sValue . ' ';

		$this->rLink = @pg_connect($sConnection, PGSQL_CONNECT_FORCE_NEW);
		$this->rLink === false and burn('DatabaseException',
			_WT('Failed to connect to database with the following message:') . "\n" . pg_last_error());

		// Set encoding
		pg_set_client_encoding($this->rLink, 'UNICODE');
	}

	/**
		Does the pgsql-dependent logic of the escape operation.

		@param	$mValue	The value to escape.
		@return	string	The escaped value.
	*/

	public function doEscape($mValue)
	{
		return "'" . pg_escape_string($mValue) . "'";
	}

	/**
		Execute an SQL query.

		@param	$sQueryString	The query string
		@return	weePgSQLResult	Only with SELECT queries: an object for results handling
	*/

	protected function doQuery($sQueryString)
	{
		$rResult = @pg_query($this->rLink, $sQueryString);
		$rResult === false and burn('DatabaseException', _WT('Failed to execute the given query:')
			. "\n" . pg_last_error($this->rLink));

		// Get it now since it can be wrong if numAffectedRows is called after getPKId
		$this->iNumAffectedRows = pg_affected_rows($rResult);

		if (pg_num_fields($rResult) > 0)
			return new weePgSQLResult($rResult);
	}

	/**
		Escape the given identifier for safe concatenation in an SQL query.

		@param	$sValue						The identifier to escape
		@return	string						The escaped identifier, wrapped around double quotes
		@throw	InvalidArgumentException	The given value is not a valid pgsql identifier.
	*/

	public function escapeIdent($sValue)
	{
		if ($sValue instanceof Printable)
			$sValue = $sValue->toString();
		$iLength = strlen($sValue);
		$iLength > 0 and $iLength <= 63 and strpos($sValue, "\0") === false
			or burn('InvalidArgumentException',
				_WT('$sValue is not a valid pgsql identifier.'));

		return '"' . str_replace('"', '""', $sValue) . '"';
	}

	/**
		Returns the name of the pgsql dbmeta class.

		@param	mixed	The name of the pgsql dbmeta class.
	*/

	public function getMetaClass()
	{
		return 'weePgSQLDbMeta';
	}

	/**
		Returns the primary key index value.

		@param	$sName	The primary key index name, if needed.
		@return	string	The primary key index value.
		@throw	IllegalStateException	No value has been generated yet for the given sequence in this session.
	*/

	public function getPKId($sName = null)
	{
		if ($sName === null)
			$sQuery = 'SELECT pg_catalog.lastval()';
		else
			$sQuery = 'SELECT pg_catalog.currval(' . $this->escape($sName) . ')';

		// We need to get the SQLSTATE returned by PostgreSQL so we can't use pg_query here.
		pg_send_query($this->rLink, $sQuery);
		$r = pg_get_result($this->rLink);

		$mSQLState = pg_result_error_field($r, PGSQL_DIAG_SQLSTATE);
		if ($mSQLState == '55000')
			burn('IllegalStateException', sprintf(_WT('%s has not yet generated a value for the given sequence in this session.'), 'PostgreSQL'));
		elseif ($mSQLState !== null)
			burn('DatabaseException', sprintf(_WT('%s failed to return the value of the given sequence with the following message:'), 'PostgreSQL')
				. "\n" . pg_last_error($this->rLink));

		return pg_fetch_result($r, 0, 0);
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return integer The number of affected rows in the last query
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}

	/**
		Prepare an SQL query statement.

		@param	$sQueryString			The query string.
		@return	weeDatabaseStatement	The prepared statement.
		@see weeDatabaseStatement
	*/

	public function prepare($sQueryString)
	{
		return new weePgSQLStatement($this->rLink, $sQueryString);
	}
}
