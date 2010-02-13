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
	Class for PDO prepared statements handling.

	Instances of this class are returned by weePDODatabase's prepare method and
	should not be instantiated manually.
*/

class weePDOStatement extends weeDatabaseStatement
{
	/**
		The database which prepared the database.
	*/

	protected $oDb;

	/**
		The number of rows affected by the last query.
	*/

	protected $iAffectedRowsCount;

	/**
		The pdo statement object.
	*/

	protected $oStatement;

	/**
		Initialises a pdo prepared statement.

		@param	$oDb		The database which protected the database.
		@param	$oStatement	The statement.
		@param	$sQuery		The query.
	*/

	public function __construct(weePDODatabase $oDb, PDOStatement $oStatement)
	{
		$this->oDb			= $oDb;
		$this->oStatement	= $oStatement;
	}

	/**
		Does the pdo-dependent work to bind the parameters to the statement.

		@param	$aParameters	The parameters to bind.
	*/

	protected function doBind($aParameters)
	{
		foreach ($aParameters as $sName => $mValue) {
			// Bool isn't supported directly, cast to int
			if ($this->oDb->is('pgsql') && is_bool($mValue))
				$mValue = (int)$mValue;
			$this->oStatement->bindValue(':' . $sName, $mValue);
		}
	}

	/**
		Executes the prepared statement.

		@return	mixed	An instance of weeDatabaseDummyResult if the query returned rows or null.
	*/

	public function execute()
	{
		$this->oStatement->execute();
		
		$a = $this->oStatement->errorInfo();
		$a[0] == '0000' or burn('DatabaseException',
			_WT('Failed to execute the statement with the following message:') . "\n" . $a[2]);

		$this->iNumAffectedRows = $this->oDb->doRowCount($this->oStatement, true);
		if ($this->oStatement->columnCount())
			return new weeDatabaseDummyResult($this->oStatement->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		You shouldn't use this method to check the number of deleted rows by a
		"DELETE FROM sometable" statement without a WHERE clause when using SQLite 2 or 3
		because it deletes and then recreates the table to increase performance,
		reporting no affected rows. Use "DELETE FROM sometable WHERE 1" if you really
		need the number of deleted rows.

		@return	int	The number of affected rows in the last query.
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}
}
