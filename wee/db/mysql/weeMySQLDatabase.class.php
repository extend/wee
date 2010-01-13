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
	MySQL database driver.
*/

class weeMySQLDatabase extends weeDatabase
{
	/**
		Link resource for this database connection.
	*/

	protected $rLink;

	/**
		The name of the underlying DBMS (mysql).
	*/

	protected $sDBMS = 'mysql';

	/**
		The number of affected rows by the last query.
	*/

	protected $iNumAffectedRows;

	/**
		Initialises a new mysql database.

		This database driver accepts the following parameters:
		 * host:		The host of the database server.
		 * user:		The user of the connection to the database.
		 * password:	The password used by the user.
		 * dbname:		The name of the database to select.
		 * encoding:	The encoding to use for the database connection. Defaults to 'utf8'.

		Refer to the documentation of mysql_connect to know the default values
		of the `host`, `user` and `password` parameters.

		This class always opens a new link to the given database.

		@param	$aParams					The parameters of the database.
		@throw	ConfigurationException		The MySQL PHP extension is missing.
		@throw	DatabaseException			Failed to connect to the database.
		@throw	InvalidArgumentException	The given encoding is invalid.
		@see	http://php.net/mysql_connect
	*/

	public function __construct($aParams = array())
	{
		function_exists('mysql_connect') or burn('ConfigurationException',
			sprintf(_WT('The %s PHP extension is required by this database driver.'), 'MySQL'));

		// mysql_connect is silenced because it triggers a warning when the connection failed.
		$this->rLink = @mysql_connect(array_value($aParams, 'host'), array_value($aParams, 'user'), array_value($aParams, 'password'), true);
		$this->rLink !== false or burn('DatabaseException',
				_WT('Failed to connect to the database with the following message:') . "\n" . mysql_error());

		if (!isset($aParams['encoding']))
			$this->doQuery('SET NAMES utf8');
		else
			try {
				$this->query('SET NAMES ?', $aParams['encoding']);
			} catch (DatabaseException $e) {
				burn('InvalidArgumentException', sprintf(_WT('Encoding "%s" is invalid.'), $aParams['encoding']));
			}

		if (isset($aParams['dbname']))
			$this->selectDb($aParams['dbname']);
	}

	/**
		Does the database-dependent logic of the escape operation.

		@param	$mValue	The value to escape.
		@return	string	The escaped value.
	*/

	protected function doEscape($mValue)
	{
		return "'" . mysql_real_escape_string($mValue, $this->rLink) . "'";
	}

	/**
		Executes an SQL query.

		@param	$sQuery			The query to execute.
		@return	weeMySQLResult	Only with SELECT queries: an object for results handling
	*/

	protected function doQuery($sQuery)
	{
		$mResult = mysql_query($sQuery, $this->rLink);
		$mResult !== false or burn('DatabaseException',
			_WT('Failed to execute the given query with the following message:') . "\n" . mysql_error($this->rLink));

		$this->iNumAffectedRows = mysql_affected_rows($this->rLink);
		if ($mResult !== true)
			return new weeMySQLResult($mResult);
	}

	/**
		Escape the given identifier for safe concatenation in an SQL query.

		@param	$sValue						The identifier to escape.
		@return	string						The escaped identifier, wrapped around ticks.
		@throw	InvalidArgumentException	The given value is not a valid mysql identifier.
	*/

	public function escapeIdent($sValue)
	{
		$iLength = strlen($sValue);
		$iLength > 0 and $iLength < 65 and strpos($sValue, "\0") === false and strpos($sValue, chr(255)) === false and substr_compare($sValue, ' ', -1)
			or burn('InvalidArgumentException', _WT('The given string is not a valid identifier.'));

		return '`' . str_replace('`', '``', $sValue) . '`';
	}

	/**
		Returns the name of the mysql dbmeta class.

		@param	mixed	The name of the mysql dbmeta class.
	*/

	public function getMetaClass()
	{
		return 'weeMySQLDbMeta';
	}

	/**
		Returns the last sequence value generated by the database in this session.

		In MySQL, it's the last value generated for an AUTO_INCREMENT column by an INSERT query.

		@param	$sName					Unused in this database driver.
		@return	string					The last value generated.
		@throw	IllegalStateException	No sequence value has been generated yet by the database in this session.
	*/

	public function getPKId($sName = null)
	{
		// Do not use mysql_insert_id here because it cannot handle BIGINT values.
		$s = $this->queryValue('SELECT LAST_INSERT_ID()');
		$s != '0' or burn('IllegalStateException',
			_WT('No sequence value has been generated yet by the database in this session.'));
		return $s;
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return	int	The number of affected rows by the last query.
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}

	/**
		Prepares an SQL query statement.

		@param	$sQuery				The query string.
		@return	weeMySQLStatement	The prepared statement.
	*/

	public function prepare($sQuery)
	{
		return new weeMySQLStatement($this, $this->rLink, $sQuery);
	}

	/**
		Changes database without reconnecting.
		The new database must be on the same host of the previous.

		@param	$sDatabase			The database to use.
		@throw	DatabaseException	Failed to select the database.
	*/

	public function selectDb($sDatabase)
	{
		mysql_select_db($sDatabase, $this->rLink) or burn('DatabaseException',
			sprintf(_WT('Failed to select the database "%s" with the following message:'), $sDatabase)
				. "\n" . mysql_error($this->rLink));
	}
}
