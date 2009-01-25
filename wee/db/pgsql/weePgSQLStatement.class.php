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
	Class for prepared statements handling.
*/

class weePgSQLStatement extends weeDatabaseStatement
{
	/**
		The pgsql link resource.
	*/

	protected $rLink;

	/**
		Number of affected rows for the previous query.
		Stocked here to prevent errors if getPKId is called.
	*/

	protected $iNumAffectedRows;

	/**
		The parameters to bind to the prepared statement.
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
		Creates a prepared statement.

		@param	$rLink						The pgsql link resource.
		@param	$mQueryString				The query string.
		@throw	InvalidArgumentException	$rLink is not a valid pgsql link resource.
		@throw	DatabaseException			PostgreSQL failed to prepare the given query.
	*/

	public function __construct($rLink, $sQueryString)
	{
		@get_resource_type($rLink) == 'pgsql link'
			or burn('InvalidArgumentException',
				_WT('$rLink is not a valid pgsql link resource.'));

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

		$this->sStatementName	= 'st_' . md5(uniqid());
		$this->rLink			= $rLink;

		$rResult = pg_prepare($this->rLink, $this->sStatementName, $sQueryString);
		$rResult !== false
			or burn('DatabaseException',
				_WT('PostgreSQL failed to prepare the given query with the following message:')
					. "\n" . pg_last_error($this->rLink));
	}

	/**
		Does the pgsql-dependent work to bind the parameters to the statement.

		@param	$aParameters	The parameters to bind.
	*/

	protected function doBind($aParameters)
	{
		foreach ($aParameters as $m => $mValue)
			if (isset($this->aParametersMap[$m]))
				$this->aParameters[$this->aParametersMap[$m]] = $mValue;
	}

	/**
		Executes the prepared statement.

		@return	mixed					An instance of weePgSQLResult if the query returned rows or null.
		@throw	IllegalStateException	The number of parameters required by the prepared statement does not match the number of bound parameters.
		@throw	DatabaseException		PostgreSQL failed to execute the prepared statement.
	*/

	public function execute()
	{
		$iParametersCount	= count($this->aParameters);
		$iMapSize			= count($this->aParametersMap);
		$iParametersCount == $iMapSize or burn('IllegalStateException',
				sprintf(_WT('The prepared statement requires %d parameters, %d were given.'),
					$iParametersCount, $iMapSize));

		ksort($this->aParameters);
		$rResult = @pg_execute($this->rLink, $this->sStatementName, $this->aParameters);
		$rResult !== false or burn('DatabaseException',
				_WT('PostgreSQL failed to execute the prepared statement with the following message:')
					. "\n" . pg_last_error($this->rLink));

		// Get it now since it can be wrong if numAffectedRows is called after getPKId
		$this->iNumAffectedRows = pg_affected_rows($rResult);

		if (pg_num_fields($rResult) > 0)
			return new weePgSQLResult($rResult);
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
