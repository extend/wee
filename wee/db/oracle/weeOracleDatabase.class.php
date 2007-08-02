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
	Oracle database driver.
*/

class weeOracleDatabase extends weeDatabase
{
	/**
		Link resource for this database connection.
	*/

	private $rLink;

	/**
		Number of affected rows for the previous query.
		Stocked here to prevent errors if getPKId is called.
	*/

	private $iNumAffectedRows;

	/**
		Number of calls to the query method.
		For informational and debugging purpose only.
	*/

	private $iNumQueries;

	/**
		Initialize the driver and connects to the database.
		The arguments available may change between drivers.

		@param $aParams Arguments for database connection, identification, and class initialization
	*/

	public function __construct($aParams, $sLogin = null, $sPassword = null)
	{
		fire(!function_exists('oci_new_connect'), 'ConfigurationException');

		putenv('NLS_LANG=UTF8');
		$this->rLink = oci_new_connect(array_value($aParams, 'user'), array_value($aParams, 'password'), array_value($aParams, 'dbname'), 'UTF8');
		fire($this->rLink === false, 'DatabaseException');

		// Initialize additional database services

		$this->iNumQueries = 0;

		$sPath = dirname(__FILE__);
		require_once($sPath . '/../weeDatabaseCriteria' . CLASS_EXT);
		require_once($sPath . '/../weeDatabaseQuery' . CLASS_EXT);

		//TODO:change the criteria class
		weeDatabaseQuery::$criteriaClass	= 'weeDatabaseCriteria';
		weeDatabaseQuery::$queryClass		= 'weeDatabaseQuery';
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
		return "'" . addslashes($mValue) . "'";
	}

	/**
		Get the last error the database returned.
		The drivers usually throw an exception when there's an error,
		but you can get the error if you catch the exception and then call this method.

		@return string The last error the database encountered
	*/

	public function getLastError()
	{
		$a = oci_error($this->rLink);
		fire(empty($a), 'IllegalStateException');

		return $a['message'];
	}

	/**
		Return the primary key index value.
		Useful when you need to retrieve the row primary key value you just inserted.
		This function may work a bit differently in each drivers.

		@param	$sName	The primary key index name, if needed
		@return	integer	The primary key index value
	*/

	public function getPKId($sName = null)
	{
		fire(empty($sName), 'InvalidParameterException');

		$rStatement = oci_parse($this->rLink, 'SELECT ' . $this->escape($sName) . '.currval FROM DUAL');
		fire($rStatement === false, 'DatabaseException');

		$b = oci_execute($rStatement, OCI_DEFAULT);
		fire(!$b, 'DatabaseException');

		$a = oci_fetch_row($rStatement);
		oci_free_statement($rStatement);

		return $a[0];
	}

	/**
		Return the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return integer The number of affected rows in the last query
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}

	/**
		Return the number of successfull queries.
		Only the queries executed using the query method are recorded.
		For informational and debugging purpose only.

		@return integer The number of queries since the creation of the class
	*/

	public function numQueries()
	{
		return $this->iNumQueries;
	}

	/**
		Execute an SQL query.

		If you pass other arguments to it, the arguments will be escaped and inserted into the query,
		using the buildSafeQuery method.

		For example if you have:
			$Db->query('SELECT ? FROM example_table WHERE example_id=? LIMIT 1', $sField, $iId);
		It will select the $sField field from the row with the $iId example_id.

		@overload query($mQueryString, $mArg1, $mArg2, ...) Example of query call with multiple arguments
		@param	$mQueryString		The query string
		@param	...					The additional arguments that will be inserted into the query
		@return	weeDatabaseResult	Only with SELECT queries: an object for results handling
	*/

	public function query($mQueryString)
	{
		$this->iNumQueries++;

		if (func_num_args() > 1)
			$mQueryString = $this->buildSafeQuery(func_get_args());
		elseif (is_object($mQueryString))
			$mQueryString = $mQueryString->build($this);

		$rStatement = oci_parse($this->rLink, $mQueryString);
		fire($rStatement === false, 'DatabaseException');

		$b = oci_execute($rStatement, OCI_DEFAULT);
		fire(!$b, 'DatabaseException');

		//TODO:probably don't work like this
		if (oci_num_rows($rStatement) > 0)
			return new weeOracleResult($rStatement);
	}
}

?>
