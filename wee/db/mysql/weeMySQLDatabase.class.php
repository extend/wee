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
	MySQL database driver.
*/

class weeMySQLDatabase extends weeDatabase
{
	/**
		Link resource for this database connection.
	*/

	protected $rLink;

	/**
		Initialize the driver and connects to the database.
		The arguments available may change between drivers.

		@param $aParams Arguments for database connection, identification, and class initialization
	*/

	public function __construct($aParams = array())
	{
		fire(!function_exists('mysql_connect'), 'ConfigurationException');

		$this->rLink = mysql_connect(array_value($aParams, 'host'), array_value($aParams, 'user'), array_value($aParams, 'password'));
		fire($this->rLink === false, 'DatabaseException');

		// Set encoding and collation

		$this->query("SET NAMES 'utf8'");
		$this->query("SET character_set_server = 'utf8'");
		$this->query("SET collation_connection = 'utf8_bin'");
		$this->query("SET collation_database = 'utf8_bin'");
		$this->query("SET collation_server = 'utf8_bin'");

		// Select database if needed

		if (!empty($aParams['dbname']))
			$this->selectDb($aParams['dbname']);

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
		@return	weeMySQLResult	Only with SELECT queries: an object for results handling
	*/

	protected function doQuery($sQueryString)
	{
		$mResult = mysql_query($sQueryString, $this->rLink);
		fire($mResult === false, 'DatabaseException');

		if (is_resource($mResult))
			return new weeMySQLResult($mResult);
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
		return "'" . mysql_real_escape_string($mValue, $this->rLink) . "'";
	}

	/**
		Gets the last error the database returned.
		The drivers usually throw an exception when there's an error,
		but you can get the error if you catch the exception and then call this method.

		@return string The last error the database encountered
	*/

	public function getLastError()
	{
		return mysql_error($this->rLink);
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
		return mysql_insert_id($this->rLink);
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return integer The number of affected rows in the last query
	*/

	public function numAffectedRows()
	{
		return mysql_affected_rows($this->rLink);
	}

	/**
		TODO

		@param	$sQueryString			The query string.
		@return	weeDatabaseStatement	The prepared statement.
		@see weeDatabaseStatement
	*/

	public function prepare($sQueryString)
	{
		burn('BadMethodCallException');
	}

	/**
		Changes database without reconnecting.
		The new database must be on the same host of the previous.

		@param $sDatabase The database to use.
	*/

	public function selectDb($sDatabase)
	{
		$b = mysql_select_db($sDatabase, $this->rLink);
		fire(!$b, 'DatabaseException');
	}
}

?>
