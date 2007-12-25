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
		Initialize the driver and connects to the database.
		The arguments available may change between drivers.

		@param $aParams Arguments for database connection, identification, and class initialization
	*/

	public function __construct($aParams = array())
	{
		fire(!function_exists('pg_connect'), 'ConfigurationException',
			'The PostgreSQL PHP extension is required by the PostgreSQL database driver.');

		//TODO:maybe quote & escape values...
		$sConnection = null;
		foreach ($aParams as $sKey => $sValue)
			$sConnection .= $sKey . '=' . $sValue . ' ';

		$this->rLink = pg_connect($sConnection, PGSQL_CONNECT_FORCE_NEW);
		fire($this->rLink === false, 'DatabaseException', 'Failed to connect to database.');

		// Set encoding

		pg_set_client_encoding($this->rLink, 'UNICODE');

		// Initialize additional database services

		$sPath = dirname(__FILE__);
		require_once($sPath . '/../weeDatabaseCriteria' . CLASS_EXT);
		require_once($sPath . '/../weeDatabaseQuery' . CLASS_EXT);

		weeDatabaseQuery::$criteriaClass	= 'weeDatabaseCriteria';
		weeDatabaseQuery::$queryClass		= 'weeDatabaseQuery';
	}

	/**
		Execute an SQL query.

		@param	$sQueryString	The query string
		@return	weePgSQLResult	Only with SELECT queries: an object for results handling
	*/

	protected function doQuery($sQueryString)
	{
		$rResult = @pg_query($this->rLink, $sQueryString);
		fire($rResult === false, 'DatabaseException', 'Failed to execute the given query: ' . $this->getLastError());

		// Get it now since it can be wrong if numAffectedRows is called after getPKId
		$this->iNumAffectedRows = pg_affected_rows($rResult);

		if (pg_num_fields($rResult) > 0)//TODO:check if it does not return > 0 with UPDATE/DELETE/...
			return new weePgSQLResult($rResult);
	}

	/**
		Escape the given value for safe concatenation in an SQL query.
		You should not build query by concatenation if possible (see query).
		You should NEVER use sprintf when building queries.

		@param	$mValue	The value to escape
		@return	string	The escaped value, wrapped around simple quotes
	*/

	public function escape($mValue)
	{
		return "'" . pg_escape_string($mValue) . "'";
	}

	/**
		Gets the last error the database returned.
		The drivers usually throw an exception when there's an error,
		but you can get the error if you catch the exception and then call this method.

		@return string The last error the database encountered
	*/

	public function getLastError()
	{
		return pg_last_error($this->rLink);
	}

	/**
		Returns the primary key index value.
		Useful when you need to retrieve the row primary key value you just inserted.
		This function may work a bit differently in each drivers.

		@param	$sName	The primary key index name, if needed
		@return	integer	The primary key index value
	*/

	public function getPKId($sName = null)
	{
		fire(empty($sName), 'InvalidArgumentException', 'Sequence name $sName must not be empty.');

		$r = pg_query($this->rLink, 'SELECT currval(' . $this->escape($sName) . ')');
		fire($r === false, 'DatabaseException', 'Failed to retrieve the value of the sequence: ' . $this->getLastError());

		return (int)pg_fetch_result($r, 0, 0);
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

?>
