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

class weeMySQLDatabase extends weeDatabase
{
	private $rLink;
	private $iNumQueries;

	public function __construct($sServer, $sLogin = null, $sPassword = null)
	{
		fire(!function_exists('mysql_connect'), 'ConfigurationException');

		$this->rLink = mysql_connect($sServer, $sLogin, $sPassword);
		fire($this->rLink === false, 'DatabaseException');

		// Set encoding and collation

		$this->query("SET NAMES 'utf8'");
		$this->query("SET character_set_server = 'utf8'");
		$this->query("SET collation_connection = 'utf8_bin'");
		$this->query("SET collation_database = 'utf8_bin'");
		$this->query("SET collation_server = 'utf8_bin'");

		// Initialize additional database services

		$this->iNumQueries = 0;

		$sPath = dirname(__FILE__);
		require_once($sPath . '/../weeDatabaseCriteria' . CLASS_EXT);
		require_once($sPath . '/../weeDatabaseQuery' . CLASS_EXT);

		weeDatabaseQuery::$criteriaClass	= 'weeMySQLCriteria';
		weeDatabaseQuery::$queryClass		= 'weeDatabaseQuery';
	}

	public function __destruct()
	{
		@mysql_close($this->rLink);
	}

	// Note:	Escape string BUT it doesn't escape %
	//			queries might be vulnerable if used with sprintf
	//TODO:don't use sprintf with queries!!

	public function escape($mValue)
	{
		fire($this->rLink === false, 'IllegalStateException');
		return "'" . mysql_real_escape_string($mValue, $this->rLink) . "'";
	}

	public function getLastError()
	{
		return mysql_error($this->rLink);
	}

	public function getLastInsertId()
	{
		fire($this->rLink === false, 'IllegalStateException');
		return mysql_insert_id($this->rLink);
	}

	public function numAffectedRows()
	{
		fire($this->rLink === false, 'IllegalStateException');
		return mysql_affected_rows($this->rLink);
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

		$mResult = mysql_query($mQueryString, $this->rLink);
		fire($mResult === false, 'DatabaseException');

		if (is_resource($mResult))
			return new weeMySQLResult($mResult);
	}

	public function selectDb($sDatabase)
	{
		$b = mysql_select_db($sDatabase, $this->rLink);
		fire(!$b, 'DatabaseException');
	}
}

?>
