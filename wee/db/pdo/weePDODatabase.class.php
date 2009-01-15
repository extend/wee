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
		The number of rows affected by the last query.
	*/

	protected $iAffectedRowsCount;

	/**
		The database object.
	*/

	protected $oDb;

	/**
		The last error message encountered.
	*/

	protected $sLastError;

	/**
		Initialises a new pdo database.

		This driver accepts the following parameters:
		 - dsn: 		The DSN of the database.
		 - user:		The user for the database.
		 - password:	The password of the user.

		@param	$aParams					The parameters of the driver.
		@throw	ConfigurationException		The PDO PHP extension is missing.
		@throw	InvalidArgumentException	The `dsn` parameter is missing.
	*/

	public function __construct($aParams = array())
	{
		class_exists('PDO', false)
			or burn('ConfigurationException',
				_WT('The PDO PHP extension is required by this database driver.'));

		isset($aParams['dsn'])
			or burn('InvalidArgumentException',
				_WT('The `dsn` parameter is missing.'));

		try
		{
			$this->oDb = new PDO($aParams['dsn'], array_value($aParams, 'user'), array_value($aParams, 'password'));
		}
		catch (PDOException $e)
		{
			burn('DatabaseException',
				_WT('PDO failed to connect to the database with the following message:')
					. "\n" . $e->getMessage());
		}
	}

	/**
		Does the pdo-dependent logic of the escape operation.

		@param	$mValue	The value to escape.
		@return	string	The escaped value.
	*/

	protected function doEscape($mValue)
	{
		return $this->oDb->quote($mValue);
	}

	/**
		Executes an SQL query.

		@param	$sQuery				The query to execute.
		@return	weeSQLiteResult		For queries that return rows, the result object.
		@throw	DatabaseException	PDO failed to execute the query.
	*/

	protected function doQuery($sQuery)
	{
		$this->sLastError = null;
		$m = $this->oDb->query($sQuery);
		if ($m === false)
		{
			$this->sLastError = array_value($this->oDb->errorInfo(), 2);
			burn('DatabaseException',
				_WT('PDO failed to execute the query with the following error:') . "\n" . $this->sLastError);
		}

		$this->iAffectedRowsCount = $m->rowCount();
		if ($m->columnCount())
			return new weePDOResult($m->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
		Escapes a given identifier for safe concatenation in an SQL query.

		@param	$sValue						The identifier to escape.
		@return	string						The escaped identifier, wrapped around adequate quotes.
		@throw	InvalidArgumentException	The given value is not a valid identifier for the current PDO driver.
		@throw	ConfigurationException		The current PDO driver is not supported by this method.
	*/

	public function escapeIdent($sValue)
	{
		switch ($this->getDriverName())
		{
			case 'mysql':
				// see weeMySQLDatabase::escapeIdent
				$iLength = strlen($sValue);
				$iLength > 0 && $iLength < 65 && strpos($sValue, "\0") === false && strpos($sValue, chr(255)) === false && substr_compare($sValue, ' ', -1)
					or burn('InvalidArgumentException',
						_WT('$sValue is not a valid mysql identifier.'));

				return '`' . str_replace('`', '``', $sValue) . '`';

			case 'oci':
				// see weeOracleDatabase::escapeIdent
				$iLength = strlen($sValue);
				strpos($sValue, '"') === false && $iLength > 0 && $iLength <= 30
					or burn('InvalidArgumentException',
						_WT('$sValue is not a valid oracle identifier.'));

				return '"' . $sValue . '"';

			case 'pgsql':
				// see weePgSQLDatabase::escapeIdent
				$iLength = strlen($sValue);
				$iLength > 0 && $iLength <= 63 && strpos($sValue, "\0") === false
					or burn('InvalidArgumentException',
						_WT('$sValue is not a valid pgsql identifier.'));

				return '"' . str_replace('"', '""', $sValue) . '"';

			case 'sqlite':
			case 'sqlite2':
				// see weeSQLiteDatabase::escapeIdent
				strlen($sValue) > 0 or burn('InvalidArgumentException',
						_WT('$sValue is not a valid sqlite identifier.'));

				return '"' . str_replace('"', '""', $sValue) . '"';

			default:
				burn('ConfigurationException',
					sprintf(_WT('Driver "%s" is not supported by the escapeIdent method.'), $this->getDriverName()));
		}
	}

	/**
		Returns the name of the PDO driver used by the database.

		@return	string	The name of the PDO driver.
	*/

	public function getDriverName()
	{
		return $this->oDb->getAttribute(PDO::ATTR_DRIVER_NAME);
	}

	/**
		Returns the name of the dbmeta class associated with the current PDO driver.

		@param	mixed	The name of the dbmeta class or null if the current PDO driver does not support dbmeta.
	*/

	public function getMetaClass()
	{
		static $aDbMetaMap = array(
			'mysql'		=> 'weeMySQLDbMeta',
			'pgsql'		=> 'weePgSQLDbMeta',
			'sqlite'	=> 'weeSQLiteDbMeta',
			'sqlite2'	=> 'weeSQLiteDbMeta'
		);

		return array_value($aDbMetaMap, $this->getDriverName());
	}

	/**
		Returns the last error the database returned.

		@return	string					The last error returned by the database.
		@throw	IllegalStateException	PDO did not return an error during the last operation.
	*/

	public function getLastError()
	{
		$this->sLastError !== null or burn('IllegalStateException',
				_WT('PDO did not returned an error during the last operation.'));
		return $this->sLastError;
	}

	/**
		Returns the primary key index value of the last inserted row.

		@param	$sName					The name of the sequence.
		@return	int						The primary key index value.
		@throw	ConfigurationException	The PDO driver does not support this capability.
	*/

	public function getPKId($sName = null)
	{
		$s = $this->oDb->lastInsertId($sName);
		$this->oDb->errorCode() != 'IM001' or burn('ConfigurationException',
			sprintf(_WT('Driver "%s" does not support this capability.'), $this->getDriverName()));
		return $s;
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.

		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return int	The number of affected rows in the last query.
	*/

	public function numAffectedRows()
	{
		return $this->iAffectedRowsCount;
	}

	/**
		Prepares an SQL query statement.

		@param	$sQuery			The query string.
		@return	weePDOStatement	The prepared statement.
	*/

	public function prepare($sQuery)
	{
		return new weePDOStatement($this->oDb, $sQuery);
	}
}
