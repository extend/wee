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
	MySQLi database driver.
*/

class weeMySQLiDatabase extends weeDatabase
{
	/**
		The MySQLi database object.
	*/

	public $oDb;

	/**
		Initialises a new mysqli database.

		This database driver accepts the following parameters:
		 - host:		The host of the database server.
		 - user:		The user of the connection to the database.
		 - password:	The password used by the user.
		 - dbname:		The name of the database to select.
		 - encoding:	The encoding to use for the database connection.

		Refer to the documentation of mysqli::real_connect() to know the default values
		of the `host`, `user` and `password` parameters.

		@param	$aParams					The parameters of the database.
		@throw	ConfigurationException		The MySQLi PHP extension is missing.
		@throw	DatabaseException			MySQLi failed to connect to the specified database.
		@throw	InvalidArgumentException	The value of the `encoding` parameter is invalid.
	*/

	public function __construct($aParams = array())
	{
		function_exists('mysqli_real_connect') or burn('ConfigurationException',
				_WT('The MySQLi PHP extension is required by the MySQLi database driver.'));

		$this->oDb = new mysqli;
		$this->oDb->init();
		$this->oDb->real_connect(array_value($aParams, 'host'), array_value($aParams, 'user'), array_value($aParams, 'password'))
			or burn('DatabaseException',
				_WT('MySQLi failed to connect to the database with the following message:')
					. "\n" . $this->oDb->connect_error);

		if (isset($aParams['encoding']))
			$this->oDb->set_charset($aParams['encoding']) or burn('InvalidArgumentException',
					sprintf(_WT('Encoding "%s" is invalid.'), $aParams['encoding']));

		if (isset($aParams['dbname']))
			$this->selectDb($aParams['dbname']);
	}

	/**
		Does the mysqli-dependent logic of the escape operation.

		@param	$mValue	The value to escape.
		@return	string	The escaped value.
	*/

	protected function doEscape($mValue)
	{
		return "'" . $this->oDb->escape_string($mValue) . "'";
	}

	/**
		Executes an SQL query.

		@param	$sQuery		The query to execute.
		@return	mixed		An instance of weeMySQLiResult or null if the query did not return a result set.
	*/

	protected function doQuery($sQuery)
	{
		$m = $this->oDb->query($sQuery);
		$m !== false or burn('DatabaseException',
				_WT('MySQLi failed to execute the given query with the following message:')
			   		. "\n" . $this->getLastError());

		if ($m !== true)
			return new weeMySQLiResult($m);
	}

	/**
		Escape the given identifier for safe concatenation in an SQL query.

		@param	$sValue	The identifier to escape.
		@return	string	The escaped identifier, wrapped around ticks.
	*/

	public function escapeIdent($sValue)
	{
		fire(
			empty($sValue) || strpos($sValue, "\0") !== false || strpos($sValue, chr(255)) !== false || !substr_compare($sValue, ' ', -1) || strlen($sValue) > 64,
			'InvalidArgumentException',
			_WT('$sValue is not a valid mysqli identifier.')
		);

		return '`' . str_replace('`', '``', $sValue) . '`';
	}

	/**
		Gets the last error the database returned.
		The drivers usually throw an exception when there's an error,
		but you can get the error if you catch the exception and then call this method.

		@return string The last error the database encountered
	*/

	public function getLastError()
	{
		return $this->oDb->error;
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
		Returns the value generated by the last INSERT query for an AUTO_INCREMENT column.

		@param	$sName					Unused in this database driver.
		@return	string					The last value generated.
		@throw	IllegalStateException	None of the previous queries generated an AUTO_INCREMENT value.
	*/

	public function getPKId($sName = null)
	{
		// Do not use mysql_insert_id() here because it cannot handle BIGINT values.
		$s = $this->queryValue('SELECT LAST_INSERT_ID()');
		$s != '0'
			or burn('IllegalStateException',
				_WT('None of the previous executed queries generated an AUTO_INCREMENT value.'));
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
		return $this->oDb->affected_rows;
	}

	/**
		Prepares an SQL query statement.

		@param	$sQuery				The query string.
		@return	weeMySQLiStatement	The prepared statement.
	*/

	public function prepare($sQuery)
	{
		return new weeMySQLStatement($this, $sQuery);
	}

	/**
		Changes database without reconnecting.
		The new database must be on the same host of the previous.

		@param	$sDatabase			The database to use.
		@throw	DatabaseException	MySQLi failed to select the database.
	*/

	public function selectDb($sDatabase)
	{
		$this->oDb->select_db($sDatabase) or burn('DatabaseException',
			sprintf(_WT('MySQLi failed to select the database "%s" with the following message:'), $sDatabase)
				. "\n" . $this->getLastError());
	}
}
