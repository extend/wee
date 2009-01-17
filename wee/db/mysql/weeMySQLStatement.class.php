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
	Class for MySQL prepared statements handling.

	Instances of this class are returned by weeMySQLDatabase's prepare method and
	should not be instantiated manually.
*/

class weeMySQLStatement extends weeDatabaseStatement
{
	/**
		The mysql database object.
	*/

	protected $oDb;

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

		@param	$oDb						The mysql database.
		@param	$sQuery						The query.
		@throw	InvalidArgumentException	$oDb is not an instance of weeMySQLDatabase nor weeMySQLiDatabase.
	*/

	public function __construct(weeDatabase $oDb, $sQuery)
	{
		$oDb instanceof weeMySQLDatabase or $oDb instanceof weeMySQLiDatabase
			or burn('InvalidArgumentException',
				_WT('$oDb must be an instance of weeMySQLDatabase or weeMySQLiDatabase.'));

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

		$this->oDb->query('PREPARE ' . $this->sStatementName . ' FROM ?', $s);
	}

	/**
		Does the mysql-dependent work to bind the parameters to the statement.

		@param	$aParameters	The parameters to bind.
	*/

	protected function doBind($aParameters)
	{
		foreach ($aParameters as $sName => $mValue)
			if (in_array($sName, $this->aParametersMap))
				$this->oDb->query('SET @_wee_' . md5($this->sStatementName . '_' . $sName) . ' = ?', $mValue);
	}

	/**
		Executes the prepared statement.

		@return	mixed	An instance of weePgSQLStatement if the query returned rows or null.
	*/

	public function execute()
	{
		$sQuery = 'EXECUTE ' . $this->sStatementName;
		if (!empty($this->aParametersMap))
		{
			$sQuery .= ' USING';
			foreach ($this->aParametersMap as $i => $sName)
				$sQuery .= ' @_wee_' . md5($this->sStatementName . '_' . $sName) . ', ';
			$sQuery = substr($sQuery, 0, -2);
		}

		$m						= $this->oDb->query($sQuery);
		$this->iNumAffectedRows	= $this->oDb->numAffectedRows();
		return $m;
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
