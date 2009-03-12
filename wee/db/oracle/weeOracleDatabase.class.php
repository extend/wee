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
	Oracle database driver.
*/

class weeOracleDatabase extends weeDatabase
{
	/**
		Link resource for this database connection.
	*/

	protected $rLink;

	/**
		The name of the underlying DBMS (oracle).
	*/

	protected $sDBMS = 'oracle';

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
			sprintf(_WT('The %s PHP extension is required by this database driver.'), 'OCI8'));

		if (!empty($aParams['encoding']))
			putenv('NLS_LANG=' . $aParams['encoding']);

		// oci_new_connect triggers a warning when the connection failed.
		$this->rLink = @oci_new_connect(array_value($aParams, 'user'), array_value($aParams, 'password'), array_value($aParams, 'dbname'), 'UTF8');
		$this->rLink !== false or burn('DatabaseException',
			_WT('Failed to connect to database with the following message:') . "\n" . array_value(oci_error(), 'message'));
	}

	/**
		Does the oracle-dependent logic of the escape operation.

		@param	$mValue	The value to escape.
		@return	string	The escaped value.
	*/

	protected function doEscape($mValue)
	{
		return "'" . str_replace("'", "''", is_bool($mValue) ? (int)$mValue : $mValue) . "'";
	}

	/**
		Execute an SQL query.

		@param	$sQueryString	The query string
		@return	weeDatabaseDummyResult	Only with SELECT queries: an object for results handling
	*/

	protected function doQuery($sQueryString)
	{
		$rStatement = oci_parse($this->rLink, $sQueryString);
		$rStatement !== false or burn('DatabaseException',
			_WT('Failed to parse the given query with the following message:') . "\n" . array_value(oci_error($this->rLink), 'message'));

		// oci_execute triggers a warning when the statement could not be executed.
		@oci_execute($rStatement, OCI_DEFAULT) or burn('DatabaseException',
			_WT('Failed to execute the given query with the following message:') . "\n" . array_value(oci_error($rStatement), 'message'));

		$this->iNumAffectedRows = oci_num_rows($rStatement);
		if (oci_num_fields($rStatement) > 0) {
			// TODO: Check whether the silence operator is really required here.
			@oci_fetch_all($rStatement, $aRows, 0, -1, OCI_ASSOC | OCI_FETCHSTATEMENT_BY_ROW);
			return new weeDatabaseDummyResult($aRows);
		}
	}

	/**
		Escape the given identifier for safe concatenation in an SQL query.

		@param	$sValue	The identifier to escape
		@return	string	The escaped identifier, wrapped around double quotes
	*/

	public function escapeIdent($sValue)
	{
		$iLength = strlen($sValue);
		strpos($sValue, '"') === false and $iLength > 0 and $iLength <= 30
			or burn('InvalidArgumentException',
				_WT('$sValue is not a valid oracle identifier.'));

		return '"' . $sValue . '"';
	}

	/**
		Returns the name of the oracle dbmeta class.

		@param	mixed	The name of the oracle dbmeta class.
	*/

	public function getMetaClass()
	{
		return 'weeOracleDbMeta';
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
		$rStatement = oci_parse($this->rLink, 'SELECT ' . $this->escapeIdent($sName) . '.currval FROM dual');
		// oci_execute triggers a warning when the statement could not be executed.
		if (!@oci_execute($rStatement, OCI_DEFAULT)) {
			$a = oci_error($rStatement);
			if ($a['code'] == 8002)
				burn('IllegalStateException', _WT('No value for the given sequence has been generated in this session.'));
			burn('DatabaseException', _WT('Failed to retrieve the value of the sequence with the following message:') . "\n" . $a['message']);
		}

		return array_value(oci_fetch_row($rStatement), 0);
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
		Prepares a given query for later execution.

		@param	$sQueryString		The query string.
		@return	weeOracleStatement	The prepared statement.
	*/

	public function prepare($sQueryString)
	{
		return new weeOracleStatement($this->rLink, $sQueryString);
	}
}
