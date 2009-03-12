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
	Oracle driver of the weeDbMeta class.
*/

class weeOracleDbMeta extends weeDbMeta
	implements weeDbMetaSchemaProvider
{
	/**
		The DBMS handled by this class (oracle).
	*/

	protected $mDBMS = 'oracle';

	/**
		Return the current schema of the database.

		@return	weeOracleDbMetaSchema	The current schema.
	*/

	public function currentSchema()
	{
		$sClass = $this->getSchemaClass();
		return new $sClass($this, $this->db()->query('
			SELECT USER AS "name" FROM SYS.DUAL
		')->fetch());
	}

	/**
		Fetch the names of the columns taking part in a given constraint.

		@param	$sSchema	The name of the schema containing the constraint.
		@param	$sName		The name of the constraint.
		@return	array		The names of the columns taking part in the constraint.
	*/

	public function fetchConstraintColumnsNames($sSchema, $sName)
	{
		$oQuery = $this->db()->query('
			SELECT		COLUMN_NAME
			FROM		SYS.ALL_CONS_COLUMNS
			WHERE		OWNER = ? AND CONSTRAINT_NAME = ?
			ORDER BY	POSITION
		', $sSchema, $sName);

		$aColumns = array();
		foreach ($oQuery as $aColumn)
			$aColumns[] = $aColumn['COLUMN_NAME'];
		return $aColumns;
	}

	/**
		Return the name of the schema class.

		@return	string	The name of the schema class.
	*/

	public function getSchemaClass()
	{
		return 'weeOracleDbMetaSchema';
	}

	/**
		Return the name of the table class.

		@return	string	The name of the table class.
	*/

	public function getTableClass()
	{
		return 'weeOracleDbMetaTable';
	}

	/**
		Query all the schemas of the database.

		@return	weeOracleResult	The data of all the schemas of the database.
	*/

	protected function querySchemas()
	{
		return $this->db()->query('
			SELECT USERNAME AS "name" FROM SYS.ALL_USERS ORDER BY USERNAME
		');
	}

	/**
		Query all the tables of the database.

		@return	weeOracleResult	The data of all the tables of the database.
	*/

	protected function queryTables()
	{
		return $this->db()->query('
			SELECT		(SELECT USER FROM DUAL) AS "schema", TABLE_NAME AS "name", t.NUM_ROWS, c.COMMENTS AS "comment"
			FROM		SYS.USER_TABLES t JOIN SYS.USER_TAB_COMMENTS c USING (TABLE_NAME)
			WHERE		t.DURATION IS NULL
			ORDER BY	TABLE_NAME
		');
	}

	/**
		Return a schema of a given name in the database.

		@param	$sName	The name of the schema.
		@return	weeOracleDbMetaSchema		The schema.
		@throw	UnexpectedValueException	The schema does not exist.
	*/

	public function schema($sName)
	{
		$oQuery = $this->db()->query('
			SELECT USERNAME AS "name" FROM SYS.ALL_USERS WHERE USERNAME = ?
		', $sName);

		count($oQuery) == 1 or burn('UnexpectedValueException',
			sprintf(_WT('Schema "%s" does not exist.'), $sName));

		$sClass = $this->getSchemaClass();
		return new $sClass($this, $oQuery->fetch());
	}

	/**
		Return whether a schema of a given name exists in the database.

		@param	$sName	The name of the schema.
		@return	bool	true if the schema exists in the database, false otherwise.
	*/

	public function schemaExists($sName)
	{
		return (bool)$this->db()->queryValue('
			SELECT COUNT(*) FROM SYS.ALL_USERS WHERE USERNAME = ? AND ROWNUM = 1
		', $sName);
	}

	/**
		Return all the schemas of the database.

		@return	array(weeOracleDbMetaSchema)	The array of schemas.
	*/

	public function schemas()
	{
		$aSchemas	= array();
		$sClass		= $this->getSchemaClass();
		foreach ($this->querySchemas() as $aSchema)
			$aSchemas[] = new $sClass($this, $aSchema);
		return $aSchemas;
	}

	/**
		Return the names of all the schemas of the database.

		@return	array(string)	The names of all the schemas.
	*/

	public function schemasNames()
	{
		$aSchemas	= array();
		$sClass		= $this->getSchemaClass();
		foreach ($this->querySchemas() as $aSchema)
			$aSchemas[] = $aSchema['name'];
		return $aSchemas;
	}

	/**
		Return a table of a given name in the database.

		@param	$sName	The name of the table.
		@return	weeOracleDbMetaTable		The table.
		@throw	UnexpectedValueException	The tables does not exist.
	*/

	public function table($sName)
	{
		$oQuery = $this->db()->query('
			SELECT	(SELECT USER FROM DUAL) AS "schema", TABLE_NAME AS "name", t.NUM_ROWS, c.COMMENTS AS "comment"
			FROM	SYS.USER_TABLES t LEFT JOIN SYS.USER_TAB_COMMENTS c USING (TABLE_NAME)
			WHERE	TABLE_NAME = ? AND t.DURATION IS NULL
		', $sName);

		count($oQuery) == 1
			or burn('UnexpectedValueException',
				sprintf(_WT('Table "%s" does not exist.'), $sName));

		$sClass = $this->getTableClass();
		return new $sClass($this, $oQuery->fetch());
	}

	/**
		Return whether a table of a given name exists in the database.

		@param	$sName	The name of the table.
		@return	bool	true if the table exists in the database, false otherwise.
	*/

	public function tableExists($sName)
	{
		return (bool)$this->db()->queryValue('
			SELECT	COUNT(*)
			FROM	SYS.USER_TABLES
			WHERE	TABLE_NAME = ? AND DURATION IS NULL AND ROWNUM = 1
		', $sName);
	}
}
