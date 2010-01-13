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
			SELECT	s.SCHEMA_NAME AS name, c.value AS comment
			FROM	INFORMATION_SCHEMA.SCHEMATA s LEFT JOIN sys.extended_properties c
						ON	c.major_id	= COALESCE(SCHEMA_ID(), SCHEMA_ID('dbo'))
						AND	c.minor_id	= 0
						AND c.class		= 3 -- c.class_desc = N'SCHEMA'
						AND	c.name		= N'MS_Description'
			WHERE	s.SCHEMA_NAME = COALESCE(SCHEMA_NAME(), 'dbo')
		")->fetch());
	}

	/**
		Fetch the names of the columns taking part in a given constraint.

		Please note this method does not check whether the given constraint really exists.

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
		Query all the schemas of the database.

		@return	weeMSSQLResult	The data of all the schemas of the database.
	*/

	protected function querySchemas()
	{
		return $this->db()->query("
			SELECT		s.SCHEMA_NAME AS name, c.value AS comment
			FROM		INFORMATION_SCHEMA.SCHEMATA s LEFT JOIN sys.extended_properties c
							ON	c.major_id	= SCHEMA_ID(s.SCHEMA_NAME)
							AND	c.minor_id	= 0
							AND c.class		= 3 -- c.class_desc = N'SCHEMA'
							AND	c.name		= N'MS_Description'
			ORDER BY	s.SCHEMA_NAME
		");
	}

	/**
		Query all the tables of the database in the default schema.

		@return	weeMSSQLResult	The data of all the tables of the database.
	*/

	protected function queryTables()
	{
		return $this->queryTablesInSchema();
	}

	/**
		Query the rows describing the tables of a given schema.
		If no schema is given, this method queries the tables in the default schema.

		Please note this method does not check whether the given schema really exists.

		@param	$sSchema		The name of the schema.
		@return	weeMSSQLResult	The tables in the given schema.
	*/

	public function queryTablesInSchema($sSchema = null)
	{
		return $this->db()->query("
			SELECT			t.TABLE_SCHEMA AS [schema], t.TABLE_NAME AS name, c.value AS comment
				FROM		INFORMATION_SCHEMA.TABLES t LEFT JOIN sys.extended_properties c
								ON	c.major_id	= OBJECT_ID(t.TABLE_SCHEMA)
								AND	c.minor_id	= 0
								AND	c.class		= 1 -- c.class_desc = 'OBJECT_OR_COLUMN'
								AND	c.name		= 'MS_Description'
				WHERE		t.TABLE_SCHEMA	= " . ($sSchema === null ? "COALESCE(SCHEMA_NAME(), 'dbo')" : ':schema') . "
					AND		t.TABLE_TYPE	= 'BASE TABLE'
				ORDER BY	t.TABLE_NAME
		", array('schema' => $sSchema));
	}

	/**
		Return a schema of a given name in the database.

		@param	$sName	The name of the schema.
		@return	weeMSSQLDbMetaSchema	The schema.
	*/

	public function schema($sName)
	{
		$oQuery = $this->db()->query("
			SELECT	TOP 1 s.SCHEMA_NAME AS name, CAST(c.value AS varchar) AS comment
			FROM	INFORMATION_SCHEMA.SCHEMATA s LEFT JOIN sys.extended_properties c
						ON	c.major_id	= SCHEMA_ID(s.SCHEMA_NAME)
						AND	c.minor_id	= 0
						AND c.class		= 3 -- c.class_desc = N'SCHEMA'
						AND	c.name		= N'MS_Description'
			WHERE	s.SCHEMA_NAME = ?
		", $sName);

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

	/**
		Return a table of a given name in the current schema.

		@param	$sName	The name of the table.
		@return	weeMSSQLDbMetaTable	The table.
	*/

	public function table($sName)
	{
		return $this->tableInSchema($sName);
	}

	/**
		Return whether a table of a given name exists in the default schema.

		@param	$sName	The name of the table.
		@return	bool	Whether the table exists.
	*/

	public function tableExists($sName)
	{
		return $this->tableExistsInSchema($sName);
	}

	/**
		Return whether a table of a given name exists in a given schema.
		If no schema is given, this method returns whether the table exists
		in the default schema.

		Please note this method will not fail if the schema itself does
		not exists.

		@param	$sName		The name of the table.
		@param	$sSchema	The name of the schema.
		@return	bool		Whether the table exists.
	*/

	public function tableExistsInSchema($sName, $sSchema = null)
	{
		return (bool)$this->db()->queryValue("
			SELECT	TOP 1 COUNT(*)
			FROM	INFORMATION_SCHEMA.TABLES
			WHERE	TABLE_NAME		= :name
				AND	TABLE_SCHEMA	= " . ($sSchema === null ? "COALESCE(SCHEMA_NAME(), 'dbo')" : ':schema') . "
				AND	TABLE_TYPE		= 'BASE TABLE'
		", array('name' => $sName, 'schema' => $sSchema));
	}

	/**
		Return a table of a given name in the current schema.
		If no schema is given, this method return the table in the default schema.

		@param	$sName		The name of the table.
		@param	$sSchema	The name of the schema.
		@return	weeMSSQLDbMetaTable			The table.
		@throw	UnexpectedValueException	The table does not exist.
	*/

	public function tableInSchema($sName, $sSchema = null)
	{
		$oQuery = $this->db()->query("
			SELECT	TOP 1 t.TABLE_SCHEMA AS [schema], t.TABLE_NAME AS name, CAST(c.value AS varchar) AS comment
			FROM	INFORMATION_SCHEMA.TABLES t LEFT JOIN sys.extended_properties c
						ON	c.major_id	= OBJECT_ID(QUOTENAME(t.TABLE_SCHEMA) + N'.' + QUOTENAME(t.TABLE_NAME))
						AND	c.minor_id	= 0
						AND c.class		= 1 -- c.class_desc = N'COLUMN_OR_OBJECT'
						AND	c.name		= N'MS_Description'
			WHERE	t.TABLE_SCHEMA	= " . ($sSchema === null ? "COALESCE(SCHEMA_NAME(), 'dbo')" : ':schema') . "
				AND	t.TABLE_NAME	= :name
				AND	t.TABLE_TYPE	= 'BASE TABLE'
		", array('name' => $sName, 'schema' => $sSchema));

		count($oQuery) == 1 or burn('UnexpectedValueException',
			_WT('The requested table does not exist.'));

		$sClass = $this->getTableClass();
		return new $sClass($this, $oQuery->fetch());
	}
}
