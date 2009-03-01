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
	Class for MySQLi prepared statements handling.

	Instances of this class are returned by weeMySQLiDatabase's prepare method and
	should not be instantiated manually.
*/

class weeMySQLiStatement extends weeDatabaseStatement
{
	/**
		The database associated with the statement.
	*/

	protected $oDb;

	/**
		The mysqli object.
	*/

	protected $oMySQLi;

	/**
		Number of affected rows for the previous query.
		Stocked here to prevent errors if getPKId is called.
	*/

	protected $iNumAffectedRows;

	/**
		The MySQL extension needs parameter names under the form ?.
		This array is the map between indexes and names.
	*/

	protected $aParametersMap = array();

	/**
		Internal PgSQL statement name, used to identify prepared statements.
		Generated automatically by this class.
	*/

	protected $sStatementName;

	/**
		Initialises a mysql prepared statement.

		@param	$oDb	The mysql link resource.
		@param	$sQuery	The query.
	*/

	public function __construct(weeDatabase $oDb, mysqli $oMySQLi, $sQuery)
	{
		preg_match_all('/:([\w_]+)/', $sQuery, $aMatches, PREG_OFFSET_CAPTURE);

		$s			= '';
		$iOffset	= 0;
		foreach ($aMatches[1] as $aMatch)
		{
			$this->aParametersMap[]	= $aMatch[0];
			$s						.= substr($sQuery, $iOffset, $aMatch[1] - $iOffset - 1) . '?';
			$iOffset				= $aMatch[1] + strlen($aMatch[0]);
		}
		$s .= substr($sQuery, $iOffset);

		$this->sStatementName	= 'st_' . md5($sQuery);
		$this->oDb				= $oDb;
		$this->oMySQLi			= $oMySQLi;

		$s = 'PREPARE ' . $this->sStatementName . ' FROM ' . $this->oDb->escape($s);
		$oMySQLi->real_query($s) or burn('DatabaseException',
			_WT('Failed to prepare the given query with the following message:') . $oMySQLi->error);
	}

	/**
		Does the mysql-dependent work to bind the parameters to the statement.

		@param	$aParameters	The parameters to bind.
	*/

	protected function doBind($aParameters)
	{
		foreach ($aParameters as $sName => $mValue)
			if (in_array($sName, $this->aParametersMap)) {
				if (is_bool($mValue))
					$mValue = (int)$mValue;
				$sQuery = 'SET @_wee_' . md5($this->sStatementName . '_' . $sName) . ' = ' . $this->oDb->escape($mValue);
				$this->oMySQLi->real_query($sQuery) or burn('DatabaseException',
					sprintf(_WT('Failed to bind parameter "%s" with the following message:'), $sName)
					. "\n" . $this->oMySQLi->error);
			}
	}

	/**
		Executes the prepared statement.

		@return	mixed	An instance of weeMySQLStatement if the query returned rows or null.
	*/

	public function execute()
	{
		$sQuery = 'EXECUTE ' . $this->sStatementName;
		if (!empty($this->aParametersMap)) {
			$sQuery .= ' USING';
			foreach ($this->aParametersMap as $i => $sName)
				$sQuery .= ' @_wee_' . md5($this->sStatementName . '_' . $sName) . ', ';
			$sQuery = substr($sQuery, 0, -2);
		}

		$mResult = $this->oMySQLi->query($sQuery);
		$mResult !== false or burn('DatabaseException',
			_WT('Failed to execute the statement with the following message:') . "\n" . $this->oMySQLi->error);

		$this->iNumAffectedRows	= $this->oMySQLi->affected_rows;
		if ($mResult !== true)
			return new weeMySQLiResult($mResult);
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return	int	The number of affected rows in the last query.
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}
}
