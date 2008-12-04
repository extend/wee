<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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
	A dummy prepared statement object for database drivers which does not support
	this feature.

	Using this class does not increase the speed on database side,
	but it does reduce the overhead induced by the weeDatabase::query method.

	Instances of this class are returned by weeDatabase's prepare method and
	should not be instantiated manually.
*/

class weeDatabaseDummyStatement extends weeDatabaseStatement
{
	/**
		The database object.
	*/

	protected $oDb;

	/**
		The extra parts of the query.
	*/

	protected $aExtraParts = array();

	/**
		The number of extra parts.
	*/

	protected $iExtraPartsCount = 0;

	/**
		The first part of the query.
	*/

	protected $sFirstPart;

	/**
		The number of affected rows by the last execution of the statement.
	*/

	protected $iNumAffectedRows;

	/**
		The parameters to bind to the prepared statement.
	*/

	protected $aParameters = array();

	/**
		The parameters map.

		This array maps query parts indexes to parameters names.
	*/

	protected $aParametersMap = array();

	/**
		Initialises a new dummy prepared statement with a given query.

		@param	$oDb	The database to use.
		@param	$sQuery	The query.
	*/

	public function __construct(weeDatabase $oDb, $sQuery)
	{
		$aMatches = array();
		preg_match_all('/:([\w_]+)/', $sQuery, $aMatches, PREG_OFFSET_CAPTURE);

		$iOffset = 0;
		foreach ($aMatches[1] as $aMatch)
		{
			$this->aParametersMap[]	= $aMatch[0];
			$this->aQueryParts[]	= substr($sQuery, $iOffset, $aMatch[1] - $iOffset - 1);
			$iOffset				= $aMatch[1] + strlen($aMatch[0]);

			$this->iExtraPartsCount++;
		}

		$this->oDb				= $oDb;
		$this->aQueryParts[]	= substr($sQuery, $iOffset);
		$this->sQueryFirstPart	= array_shift($this->aQueryParts);
	}

	/**
		Does the database-dependent work to bind the parameters to the statement.

		@param	$aParameters	The parameters to bind.
	*/

	protected function doBind($aParameters)
	{
		$this->aParameters = $aParameters + $this->aParameters;
	}

	/**
		Executes the prepared statement.

		@return	mixed	An instance of weeDatabaseStatement if the query returned rows or null.
	*/

	public function execute()
	{
		$sQuery = $this->sQueryFirstPart;
		for ($i = 0; $i < $this->iExtraPartsCount; ++$i)
		{
			$sName = $this->aParametersMap[$i];
			is_array($this->aParameters) ? array_key_exists($sName, $this->aParameters) : isset($this->aParameters[$sName])
				or burn('IllegalStateException',
					sprintf(_WT('The value of the `%s` parameter is missing.'), $sName));

			$sQuery .= $this->oDb->escape($this->aParameters[$sName]) . $this->aQueryParts[$i];
		}

		$m = $this->oDb->query($sQuery);
		$this->iNumAffectedRows = $this->oDb->numAffectedRows();
		return $m;
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return	int		The number of affected rows by the last execution of the statement.
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}
}
