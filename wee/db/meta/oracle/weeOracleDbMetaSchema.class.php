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
	Oracle specialisation of weeDbMetaSchema.
*/

class weeOracleDbMetaSchema extends weeDbMetaSchema
{
	/**
		Initialise a new oracle schema object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeOracleDbMeta.

		@param	$oMeta	The oracle dbmeta object.
		@param	$aData	The schema data.
	*/

	public function __construct(weeOracleDbMeta $oMeta, array $aData)
	{
		parent::__construct($oMeta, $aData);
	}

	/**
		Query all the tables in the schema.

		@return	weeOracleResult	The data of all the tables in the schema.
	*/

	protected function queryTables()
	{
		return $this->db()->query('
			SELECT		OWNER AS "schema", TABLE_NAME AS "name", t.NUM_ROWS, c.COMMENTS AS "comment"
			FROM		SYS.ALL_TABLES t LEFT JOIN SYS.ALL_TAB_COMMENTS c USING (OWNER, TABLE_NAME)
			WHERE		OWNER = ? AND t.DURATION IS NULL
			ORDER BY	TABLE_NAME
		', $this->name());
	}

	/**
		Return a table of a given name in the schema.

		@param	$sName	The name of the table.
		@return	weeOracleDbMetaTable		The table.
		@throw	UnexpectedValueException	The table does not exist in the schema.
	*/

	public function table($sName)
	{
		$oQuery = $this->meta()->db()->query('
			SELECT	OWNER AS "schema", TABLE_NAME AS "name", t.NUM_ROWS, c.COMMENTS AS "comment"
			FROM	SYS.ALL_TABLES t LEFT JOIN SYS.ALL_TAB_COMMENTS c USING (OWNER, TABLE_NAME)
			WHERE	TABLE_NAME	= :table
				AND	OWNER		= :name
				AND	t.DURATION IS NULL
		', array('table' => $sName) + $this->aData);

		count($oQuery) == 1 or burn('UnexpectedValueException',
			sprintf(_WT('Table "%s" does not exist in the schema.'), $sName));

		$sClass = $this->meta()->getTableClass();
		return new $sClass($this->meta(), $oQuery->fetch());
	}

	/**
		Return whether a table of a given name exists in the schema.

		@param	$sName	The name of the table.
		@return	bool	Whether the table exists in the schema.
	*/

	public function tableExists($sName)
	{
		return (bool)$this->db()->queryValue('
			SELECT	COUNT(*)
			FROM	SYS.ALL_TABLES
			WHERE	TABLE_NAME = :table AND OWNER = :name AND DURATION IS NULL AND ROWNUM = 1
		', array('table' => $sName) + $this->aData);
	}
}
