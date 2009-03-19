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
	PDO database driver.
*/

class weePDODatabase extends weeDatabase
{
	/**
		The database object.
	*/

	protected $oDb;

	/**
		The number of rows affected by the last query.
	*/

	protected $iNumAffectedRows;

	/**
		Initialises a new pdo database.

		This driver accepts the following parameters:
		 - dsn: 		The DSN of the database.
		 - user:		The user for the database.
		 - password:	The password of the user.

		@param	$aParams					The parameters of the driver.
		@throw	ConfigurationException		The PDO PHP extension is missing.
		@throw	InvalidArgumentException	Parameter "dsn" is missing.
		@throw	DatabaseException			Failed to connect to database.
	*/

	public function __construct($aParams = array())
	{
		class_exists('PDO', false) or burn('ConfigurationException',
			sprintf(_WT('The %s PHP extension is required by this database driver.'), 'PDO'));

		isset($aParams['dsn']) or burn('InvalidArgumentException',
			sprintf(_WT('Parameter "%s" is missing.'), 'dsn'));

		try {
			$this->oDb = new PDO($aParams['dsn'], array_value($aParams, 'user'), array_value($aParams, 'password'));
		} catch (PDOException $e) {
			if ($e->getCode() == 'IM003')
				burn('ConfigurationException', _WT('The requested PDO driver is missing.'));
			burn('DatabaseException', _WT('Failed to connect to the database with the following message:')
				. "\n" . $e->getMessage());
		}

		$sDriver = $this->oDb->getAttribute(PDO::ATTR_DRIVER_NAME);
		if ($sDriver == 'oci')
			$this->sDBMS = 'oracle';
		elseif ($sDriver == 'dblib')
			$this->sDBMS = 'mssql';
		else
			$this->sDBMS = $sDriver;
	}

	/**
		Does the pdo-dependent logic of the escape operation.

		@param	$mValue	The value to escape.
		@return	string	The escaped value.
	*/

	protected function doEscape($mValue)
	{
		// see http://wee.extend.ws/ticket/73
		if ($this->sDBMS == 'pgsql' && is_bool($mValue))
			$mValue = (int)$mValue;
		return $this->oDb->quote($mValue);
	}

	/**
		Executes an SQL query.

		@param	$sQuery	The query to execute.
		@return	weeDataseDummyResult	For queries that return rows, the result object.
		@throw	DatabaseException		Failed to execute the query.
	*/

	protected function doQuery($sQuery)
	{
		// PDO::query triggers a warning when calling an undefined stored procedure when
		// used with PDO_DBLIB driver.
		$m = @$this->oDb->query($sQuery);
		$m !== false or burn('DatabaseException', _WT('Failed to execute the query with the following error:')
			. "\n" . array_value($this->oDb->errorInfo(), 2));

		$this->iNumAffectedRows = $this->doRowCount($m);
		// PDO_DBLIB always return 0 for the column count of an empty result set,
		// even with SELECT queries.
		if ($m->columnCount() || $this->sDBMS == 'mssql' && substr(ltrim($sQuery), 0, 6) == 'SELECT')
			return new weeDatabaseDummyResult($m->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
		Does the driver-dependent work of PDOStatement::rowCount.

		You should NOT call this method, its only use is to work around
		a inconsistency in the PDO_SQLITE2 driver.

		@param	$oStatement		The PDO statement.
		@param	$bIsPrepared	Whether $oStatement comes from a prepared statement.
		@return	int				The number of affected rows by the last execution of the statement.
		@see	http://wee.extend.ws/ticket/71
	*/

	public function doRowCount(PDOStatement $oStatement, $bIsPrepared = false)
	{
		$iRowCount = $oStatement->rowCount();

		if ($this->sDBMS == 'sqlite2') {
			if ($bIsPrepared && !isset($iNumAffectedRows))
				// A prepared statement is executed for the first time, begin tracking of the number of
				// affected rows reported by SQLite 2.
				static $iNumAffectedRows = 0;

			if (!$oStatement->columnCount() && isset($iNumAffectedRows)) {
				// Prepared statements have been used and $oStatement is not a SELECT query,
				// we need to return the difference between the new and old reported numbers of affected rows.
				$i = $iRowCount - $iNumAffectedRows;
				$iNumAffectedRows = $iRowCount;
				return $i;
			}
		}

		return $iRowCount;
	}

	/**
		Escape a given identifier for safe concatenation in an SQL query.

		In SQLite 2, be careful when using escaped identifiers in the field list of a SELECT query as
		they will be used as the keys of the result set.

		@param	$sValue	The identifier to escape.
		@return	string	The escaped identifier, wrapped around adequate quotes.
		@throw	InvalidArgumentException	The given value is not a valid identifier for the current PDO driver.
		@throw	ConfigurationException		The current PDO driver is not supported by this method.
	*/

	public function escapeIdent($sValue)
	{
		switch ($this->sDBMS)
		{
			case 'mssql':
				// see weeMSSQLDatabase::escapeIdent and weeSQLiteDatabase
				$i = strlen($sValue);
				$i != 0 && $i < 129 or burn('InvalidArgumentException',
					_WT('The given value is not a valid identifier.'));
			case 'sqlite':
			case 'sqlite2':
				return '[' . str_replace(']', ']]', $sValue) . ']';

			case 'mysql':
				// see weeMySQLDatabase::escapeIdent
				$iLength = strlen($sValue);
				$iLength > 0 && $iLength < 65 && strpos($sValue, "\0") === false && strpos($sValue, chr(255)) === false && substr_compare($sValue, ' ', -1)
					or burn('InvalidArgumentException', _WT('The given string is not a valid identifier.'));
				return '`' . str_replace('`', '``', $sValue) . '`';

			case 'oracle':
				// see weeOracleDatabase::escapeIdent
				$iLength = strlen($sValue);
				strpos($sValue, '"') === false && $iLength > 0 && $iLength <= 30 or burn('InvalidArgumentException',
					_WT('The given string is not a valid identifier.'));
				return '"' . $sValue . '"';

			case 'pgsql':
				// see weePgSQLDatabase::escapeIdent
				$iLength = strlen($sValue);
				$iLength > 0 && $iLength <= 63 && strpos($sValue, "\0") === false or burn('InvalidArgumentException',
					_WT('The given string is not a valid identifier.'));
				return '"' . str_replace('"', '""', $sValue) . '"';

			default:
				burn('ConfigurationException', sprintf(_WT('Driver "%s" does not support this capability.'), $this->sDBMS));
		}
	}

	/**
		Returns the name of the dbmeta class associated with the current PDO driver.

		@param	mixed	The name of the dbmeta class or null if the current PDO driver does not support dbmeta.
	*/

	public function getMetaClass()
	{
		static $aDbMetaMap = array(
			'mssql'		=> 'weeMSSQLDbMeta',
			'mysql'		=> 'weeMySQLDbMeta',
			'pgsql'		=> 'weePgSQLDbMeta',
			'sqlite'	=> 'weeSQLiteDbMeta',
			'sqlite2'	=> 'weeSQLiteDbMeta',
		);

		return array_value($aDbMetaMap, $this->sDBMS);
	}

	/**
		Returns the last sequence value generated by the database in this session.

		@param	$sName					The name of the sequence.
		@return	int						The primary key index value.
		@throw	ConfigurationException	The PDO driver does not support this capability.
		@throw	IllegalStateException	No value has been generated yet for the given sequence name.
		@throw	DatabaseException		An error occured during the retrieving of the last generated value.
	*/

	public function getPKId($sName = null)
	{
		if ($this->sDBMS == 'mysql' || substr($this->sDBMS, 0, 6) == 'sqlite') {
			$s = $this->oDb->lastInsertId($sName);
			$s != '0' or burn('IllegalStateException', _WT('No sequence value has been generated yet by the database in this session.'));
			return $s;
		}

		if ($this->sDBMS == 'oracle') {
			$o = $this->oDb->query('SELECT ' . $this->escapeIdent($sName) . '.CURRVAL FROM DUAL');
			$a = $this->oDb->errorInfo();
			if (isset($a[1]) && $a[1] == 8002)
				burn('IllegalStateException', _WT('No sequence value has been generated yet by the database in this session.'));
			elseif ($a[0] != '00000')
				burn('DatabaseException', _WT('Failed to return the value of the given sequence with the following message:') . "\n" . $a[2]);
			return $o->fetchColumn(0);
		}

		if ($this->sDBMS == 'pgsql' && $sName === null) {
			$o = $this->oDb->query('SELECT pg_catalog.lastval()');
			$a = $this->oDb->errorInfo();
			if ($a[0] == '55000')
				burn('IllegalStateException', _WT('No sequence value has been generated yet by the database in this session.'));
			elseif ($a[0] != '00000')
				burn('DatabaseException', _WT('Failed to return the value of the given sequence with the following message:') . "\n" . $a[2]);
			return $o->fetchColumn(0);
		}

		// PDO::lastInsertId must be silenced because it triggers a warning when the PDO driver is not supported
		// by this method.

		$s = @$this->oDb->lastInsertId($sName);
		$a = $this->oDb->errorInfo();
		if ($a[0] == '55000')
			burn('IllegalStateException', _WT('No sequence value has been generated yet by the database in this session.'));
		elseif ($a[0] == 'IM001')
			burn('ConfigurationException', sprintf(_WT('Driver "%s" does not support this capability.'), $this->oDb->getAttribute(PDO::ATTR_DRIVER_NAME)));
		elseif ($a[0] != '00000')
			burn('DatabaseException', _WT('Failed to return the value of the given sequence with the following message:') . "\n" . $a[2]);
		return $s;
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.

		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		You shouldn't use this method to check the number of deleted rows by a
		"DELETE FROM sometable" query without a WHERE clause when using SQLite 2 or 3
		because it deletes and then recreates the table to increase performance,
		reporting no affected rows. Use "DELETE FROM sometable WHERE 1" if you really
		need the number of deleted rows.

		@return int	The number of affected rows in the last query.
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}

	/**
		Prepares an SQL query statement.

		@param	$sQuery			The query string.
		@return	weePDOStatement	The prepared statement.
	*/

	public function prepare($sQuery)
	{
		try {
			return new weePDOStatement($this, $this->oDb->prepare($sQuery));
		} catch (PDOException $e) {
			burn('DatabaseException', _WT('Failed to prepare the query with the following message:') . "\n" . $e->getMessage());
		}
	}
}
