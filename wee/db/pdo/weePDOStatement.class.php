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
	Class for PDO prepared statements handling.

	Instances of this class are returned by weePDODatabase's prepare method and
	should not be instantiated manually.
*/

class weePDOStatement extends weeDatabaseStatement
{
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

		@param	$oDb	The pdo database.
		@param	$sQuery	The query.
	*/

	public function __construct(PDO $oDb, $sQuery)
	{
		try
		{
			$this->oStatement = $oDb->prepare($sQuery);
		}
		catch (PDOException $e)
		{
			burn('DatabaseException',
				_WT('PDO failed to prepare the query with the following message:')
					. "\n" . $e->getMessage());
		}
	}

	/**
		Does the pdo-dependent work to bind the parameters to the statement.

		@param	$aParameters	The parameters to bind.
	*/

	protected function doBind($aParameters)
	{
		foreach ($aParameters as $sName => $mValue)
			$this->oStatement->bindValue(':' . $sName, $mValue);
	}

	/**
		Executes the prepared statement.

		@return	mixed	An instance of weePDOResult if the query returned rows or null.
	*/

	public function execute()
	{
		$this->oStatement->execute() or burn('DatabaseException',
			_WT('PDO failed to execute the statement with the following message:')
				. "\n" . array_value($this->oStatement->errorInfo(), 2));

		$this->iNumAffectedRows = $this->oStatement->rowCount();
		if ($this->oStatement->columnCount())
			return new weePDOResult($this->oStatement->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		There is a bug in PHP 5.2.6 (and possibly all the previous versions) that make
		PDO unable to retrieve the number of affected rows when using the SQLite driver.
		See http://bugs.php.net/bug.php?id=46007 for more details. This seems to be
		fixed as of PHP 5.2.7.

		@return	int	The number of affected rows in the last query.
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}
}
