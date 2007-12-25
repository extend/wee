<?php

/*
	Web:Extend
	Copyright (c) 2007 Dev:Extend

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
	Class for prepared statements handling.
*/

class weePgSQLStatement extends weeDatabaseStatement
{
	/**
		Number of affected rows for the previous query.
		Stocked here to prevent errors if getPKId is called.
	*/

	protected $iNumAffectedRows;

	/**
		The PgSQL extension bind parameters when executing it.
		The array must contain the values with 0 corresponding to $1,
		1 to $2 and so on...
	*/

	protected $aParameters = array();

	/**
		The PgSQL extension needs parameter names under the form $n.
		This array is the map between names and numbers.
	*/

	protected $aParametersMap = array();

	/**
		Internal PgSQL statement name, used to identify prepared statements.
		Generated automatically by this class.
	*/

	protected $sStatementName;

	/**
		Create a prepared statement.

		@param $rLink The database link
		@param $mQueryString The query string
	*/

	public function __construct($rLink, $sQueryString)
	{
		parent::__construct($rLink, $sQueryString);

		// Get parameters name and position

		$aMatches = array();
		preg_match_all('/:([\w_]+)/', $sQueryString, $aMatches);
		arsort($aMatches[1]);

		$i = 1;
		foreach ($aMatches[1] as $sName)
			if (empty($this->aParametersMap[$sName]))
				$this->aParametersMap[$sName] = $i++;

		// Replace named parameters by $n

		foreach ($this->aParametersMap as $sName => $i)
			$sQueryString = str_replace(':' . $sName, '$' . $i, $sQueryString);

		// Prepare the statement

		$this->sStatementName = 'st_' . md5($sQueryString);

		$rResult = pg_prepare($this->rLink, $this->sStatementName, $sQueryString);
		fire($rResult === false, 'DatabaseException', 'Failed to prepare the statement: ' . pg_last_error($this->rLink));
	}

	/**
		Bind parameters to the statement.

		You can pass either one parameter name and its value,
		or an array of parameters that will be binded.

		@overload bind($sName, $sValue) Example of query call with one parameter instead of an array
		@param	$aBindParams The parameters to bind to the statement
		@return	$this
	*/

	public function bind($aBindParams)
	{
		if (func_num_args() == 2)
			$aBindParams = array(func_get_arg(0), func_get_arg(1));

		foreach ($aBindParams as $sName => $sValue)
			$this->aParameters[$this->aParametersMap[$sName]] = $sValue;

		return $this;
	}

	/**
		Execute the prepared statement.

		@return weeDatabaseResult Only with SELECT queries: an object for results handling
	*/

	public function execute()
	{
		fire(sizeof($this->aParameters) != sizeof($this->aParametersMap), 'IllegalStateException',
			'The prepared statement requires ' . sizeof($this->aParametersMap) . ' parameters, but ' .
			sizeof($this->aParameters) . ' were given.');

		ksort($this->aParameters);
		$rResult = @pg_execute($this->rLink, $this->sStatementName, $this->aParameters);
		fire($rResult === false, 'DatabaseException',
			'Failed to execute the prepared statement: ' . pg_last_error($this->rLink));

		// Get it now since it can be wrong if numAffectedRows is called after getPKId
		$this->iNumAffectedRows = pg_affected_rows($rResult);

		if (pg_num_fields($rResult) > 0)//TODO:check if it does not return > 0 with UPDATE/DELETE/...
			return new weePgSQLResult($rResult);
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return integer The number of affected rows in the last query
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}
}

?>