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
		The mysql link resource.
	*/

	protected $rLink;

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
		@throw	InvalidArgumentException	The database is not an instance of weeMySQLDatabase nor weeMySQLiDatabase.
	*/

	public function __construct(weeDatabase $oDb, $rLink, $sQuery)
	{
		is_resource($rLink) && get_resource_type($rLink) == 'mysql link' or burn('InvalidArgumentException',
			sprintf(_WT('The given variable must be a resource of type "%s".'), 'mysql link'));
		$oDb->is('mysql') or burn('InvalidArgumentException',
			_WT('The underlying DBMS of the given database is not handled by this class.'));

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
		$this->rLink			= $rLink;

		$s = 'PREPARE ' . $this->sStatementName . ' FROM ' . $this->oDb->escape($s);
		mysql_unbuffered_query($s, $this->rLink) !== false or burn('DatabaseException',
			_WT('Failed to prepare the given query with the following message:') . "\n" . mysql_error($this->rLink));
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
				mysql_unbuffered_query($sQuery, $this->rLink) !== false or burn('DatabaseException',
					sprintf(_WT('Failed to bind parameter "%s" with the following message:'), $sName)
					. "\n" . mysql_error($this->rLink));
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

		$mResult = mysql_query($sQuery, $this->rLink);
		$mResult !== false or burn('DatabaseException',
			_WT('Failed to execute the statement with the following message:') . "\n" . mysql_error($this->rLink));

		$this->iNumAffectedRows	= mysql_affected_rows($this->rLink);
		if ($mResult !== true)
			return new weeMySQLResult($mResult);
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
