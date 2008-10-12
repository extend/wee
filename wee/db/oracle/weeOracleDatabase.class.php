<?php

/*
	Web:Extend
	Copyright (c) 2006, 2008 Dev:Extend

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
		The last error recorded.
	*/

	protected $sLastError;

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
		function_exists('oci_new_connect') or burn('ConfigurationException',
			'The OCI8 PHP extension is required by the Oracle database driver.');

		if (!empty($aParams['encoding']))
			putenv('NLS_LANG=' . $aParams['encoding']);

		$this->rLink = @oci_new_connect(array_value($aParams, 'user'), array_value($aParams, 'password'), array_value($aParams, 'dbname'), 'UTF8');

		if ($this->rLink === false)
		{
			$this->setLastError(oci_error());
			burn('DatabaseException', 'Failed to connect to database.');
		}
	}

	/**
		Execute an SQL query.

		@param	$sQueryString	The query string
		@return	weeOracleResult	Only with SELECT queries: an object for results handling
	*/

	protected function doQuery($sQueryString)
	{
		$rStatement = oci_parse($this->rLink, $sQueryString);

		if ($rStatement === false)
		{
			$this->setLastError(oci_error($rStatement));
			burn('DatabaseException', 'Failed to parse the given query.');
		}

		if (!@oci_execute($rStatement, OCI_DEFAULT))
		{
			$this->setLastError(oci_error($rStatement));
			burn('DatabaseException', 'Failed to execute the given query.');
		}

		if (oci_num_fields($rStatement) > 0)
			return new weeOracleResult($rStatement);

		$this->iNumAffectedRows = oci_num_rows($rStatement);
		oci_free_statement($rStatement);
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
		if ($mValue === null)
			return 'null';
		elseif ($mValue instanceof Printable)
			$mValue = $mValue->toString();

		return "'" . addslashes($mValue) . "'";
	}

	/**
		Escape the given identifier for safe concatenation in an SQL query.

		@param	$sValue	The identifier to escape
		@return	string	The escaped identifier, wrapped around double quotes
	*/

	public function escapeIdent($sValue)
	{
		fire(empty($sValue) || strpos($sValue, "\0") !== false || strlen($sValue) > 63, 'InvalidArgumentException',
			_('$sValue is not a valid pgsql identifier.'));

		return '"' . str_replace('"', '""', $sValue) . '"';
	}

	/**
		Get the last error the database returned.
		The drivers usually throw an exception when there's an error,
		but you can get the error if you catch the exception and then call this method.

		@return string The last error the database encountered
	*/

	public function getLastError()
	{
		return $this->sLastError;
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
		fire(empty($sName), 'InvalidArgumentException', 'The argument $sName is required.');

		$rStatement = oci_parse($this->rLink, 'SELECT ' . $this->escapeIdent($sName) . '.currval FROM DUAL');

		if ($rStatement === false)
		{
			$this->setLastError(oci_error($rStatement));
			burn('DatabaseException', 'Failed to parse the query to retrieve the value of the sequence.');
		}

		if (!@oci_execute($rStatement, OCI_DEFAULT))
		{
			$this->setLastError(oci_error($rStatement));
			echo $this->getLastError();
			burn('DatabaseException', 'Failed to retrieve the value of the sequence.');
		}

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
		TODO

		@param	$sQueryString			The query string.
		@return	weeDatabaseStatement	The prepared statement.
		@see weeDatabaseStatement
	*/

	public function prepare($sQueryString)
	{
		burn('BadMethodCallException', 'This method is not implemented yet.');
	}

	/**
		Set the last error encountered while using the oracle extension. Used internally.

		@param $aError The array returned by oci_error where the error occurred.
	*/

	public function setLastError($aError)
	{
		$this->sLastError = empty($aError['message']) ? 'Unknown error.' : $aError['message'];
	}
}
