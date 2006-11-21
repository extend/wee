<?php

/*
	Web:Extend
	Copyright (c) 2006 Dev:Extend

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

class weePgSQLDatabase extends weeDatabase
{
	private $rLink;
	private $iNumAffectedRows;
	private $iNumQueries;

	public function __construct($aParams, $sLogin = null, $sPassword = null)
	{
		fire(!function_exists('pg_connect'), 'ConfigurationException');

		//TODO:maybe quote & escape values...
		$sConnection = null;
		foreach ($aParams as $sKey => $sValue)
			$sConnection .= $sKey . '=' . $sValue . ' ';

		$this->rLink = pg_connect($sConnection, PGSQL_CONNECT_FORCE_NEW);
		fire($this->rLink === false, 'DatabaseException');

		// Set encoding

		pg_set_client_encoding($this->rLink, 'UNICODE');

		// Initialize additional database services

		$this->iNumQueries = 0;

		$sPath = dirname(__FILE__);
		require_once($sPath . '/../weeDatabaseCriteria' . CLASS_EXT);
		require_once($sPath . '/../weeDatabaseQuery' . CLASS_EXT);

		//TODO:change the criteria class
		weeDatabaseQuery::$criteriaClass	= 'weeDatabaseCriteria';
		weeDatabaseQuery::$queryClass		= 'weeDatabaseQuery';
	}

	public function __destruct()
	{
		pg_close($this->rLink);
	}

	// Note:	Escape string BUT it doesn't escape %
	//			queries might be vulnerable if used with sprintf
	//TODO:don't use sprintf with queries!!
	//TODO:pg_escape_bytea?

	public function escape($mValue)
	{
		return "'" . pg_escape_string($mValue) . "'";
	}

	public function getLastError()
	{
		return pg_last_error($this->rLink);
	}

	public function getPKId($sName = null)
	{
		fire(empty($sName), 'InvalidParameterException');
		fire($this->rLink === false, 'IllegalStateException');

		$r = pg_query($this->rLink, 'SELECT currval(' . $this->escape($sName) . ')');
		fire($r === false, 'DatabaseException');

		return (int)pg_fetch_result($r, 0, 0);
	}

	public function numAffectedRows()
	{
		fire($this->rLink === false, 'IllegalStateException');
		return $this->iNumAffectedRows;
	}

	public function numQueries()
	{
		return $this->iNumQueries;
	}

	public function query($mQueryString)
	{
		fire($this->rLink === false, 'IllegalStateException');

		$this->iNumQueries++;

		if (func_num_args() > 1)
			$mQueryString = $this->buildSafeQuery(func_get_args());
		elseif (is_object($mQueryString))
			$mQueryString = $mQueryString->build($this);

		$rResult = @pg_query($this->rLink, $mQueryString);
		fire($rResult === false, 'DatabaseException');

		// Get it now since it can be wrong if numAffectedRows is called after getPKId
		$this->iNumAffectedRows = pg_affected_rows($rResult);

		if (pg_num_fields($rResult) > 0)//TODO:check if it does not return > 0 with UPDATE/DELETE/...
			return new weePgSQLResult($rResult);
	}
}

?>
