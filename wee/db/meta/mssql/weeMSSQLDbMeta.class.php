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
	MSSQL driver of the weeDbMeta class
*/

class weeMSSQLDbMeta extends weeDbMeta implements weeDbMetaSchemaProvider
{
	/**
		The DBMS handled by this class (mssql).
	*/

	protected $mDBMS = 'mssql';

	/**
		Return the current schema of the database.

		@return	weeDbMetaSchema		The current schema.
	*/

	public function currentSchema()
	{
		$sClass = $this->getSchemaClass();
		return new $sClass($this, $this->db()->query("
			SELECT TOP 1 SCHEMA_NAME AS name
			FROM INFORMATION_SCHEMA.SCHEMATA
			WHERE SCHEMA_NAME = COALESCE(SCHEMA_NAME(), 'dbo')
		")->fetch());
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
			SELECT			COLUMN_NAME
				FROM		INFORMATION_SCHEMA.KEY_COLUMN_USAGE
				WHERE		CONSTRAINT_SCHEMA	= ?
						AND	CONSTRAINT_NAME		= ?
				ORDER BY	ORDINAL_POSITION
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
		return 'weeMSSQLDbMetaSchema';
	}

	/**
		Return the name of the table class.

		@return	string	The name of the table class.
	*/

	public function getTableClass()
	{
		return 'weeMSSQLDbMetaTable';
	}

	/**
		Return a table of a given name in the database.

		@param	$sName						The name of the table.
		@return	weeMSSQLDbMetaTable			The table.
		@throw	UnexpectedValueException	The table does not exist.
	*/

	public function table($sName)
	{
		$oQuery = $this->db()->query("
			SELECT		TOP 1 TABLE_SCHEMA AS [schema], TABLE_NAME AS name
				FROM	INFORMATION_SCHEMA.TABLES
				WHERE	TABLE_NAME		= ?
					AND	TABLE_SCHEMA	= COALESCE(SCHEMA_NAME(), 'dbo')
					AND	TABLE_TYPE		= 'BASE TABLE'
		", $sName);

		count($oQuery) == 1 or burn('UnexpectedValueException',
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
		return $this->db()->queryValue("
			SELECT	TOP 1 COUNT(*)
			FROM	INFORMATION_SCHEMA.TABLES
			WHERE	TABLE_NAME = ? AND TABLE_SCHEMA = COALESCE(SCHEMA_NAME(), 'dbo') AND TABLE_TYPE = 'BASE TABLE'
		", $sName);
	}

	/**
		Query all the schemas of the database.

		@return	weeMSSQLResult	The data of all the schemas of the database.
	*/

	protected function querySchemas()
	{
		return $this->db()->query("
			SELECT		SCHEMA_NAME AS name
			FROM		INFORMATION_SCHEMA.SCHEMATA
			ORDER BY	SCHEMA_NAME
		");
	}

	/**
		Query all the tables of the database.

		@return	weeMSSQLResult	The data of all the tables of the database.
	*/

	protected function queryTables()
	{
		return $this->db()->query("
			SELECT			TABLE_SCHEMA AS [schema], TABLE_NAME AS name
				FROM		INFORMATION_SCHEMA.TABLES
				WHERE		TABLE_SCHEMA	= COALESCE(SCHEMA_NAME(), 'dbo')
					AND		TABLE_TYPE		= 'BASE TABLE'
				ORDER BY	name
		");
	}

	/**
		Return a schema of a given name in the database.

		@param	$sName	The name of the schema.
		@return	weeMSSQLDbMetaSchema	The schema.
	*/

	public function schema($sName)
	{
		$oQuery = $this->db()->query('
			SELECT	TOP 1 SCHEMA_NAME AS name
			FROM	INFORMATION_SCHEMA.SCHEMATA
			WHERE	SCHEMA_NAME = ?
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
			SELECT TOP 1 COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?
		', $sName);
	}

	/**
		Return all the schemas of the database.

		@return	array(weeMSSQLDbMetaSchema)	The array of schemas.
	*/

	public function schemas()
	{
		$sClass		= $this->getSchemaClass();
		$aSchemas	= array();
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
		$aNames = array();
		foreach ($this->querySchemas() as $aSchema)
			$aNames[] = $aSchema['name'];
		return $aNames;
	}
}
