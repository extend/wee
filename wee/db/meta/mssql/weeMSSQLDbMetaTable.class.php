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
	MSSQL specialisation of weeDbMetaTable.
*/

class weeMSSQLDbMetaTable extends weeDbMetaTable
	implements weeDbMetaForeignKeyProvider, weeDbMetaSchemaObject
{
	/**
		Initialise a new mssql table object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeMSSQLDbMeta.

		@param	$oMeta	The mssql dbmeta object.
		@param	$aData	The object data.
	*/

	public function __construct(weeMSSQLDbMeta $oMeta, array $aData)
	{
		parent::__construct($oMeta, $aData);
	}

	/**
		Return a column of the table.

		@param	$sName	The column name.
		@return	weeMSSQLDbMetaColumn	The column.
	*/

	public function column($sName)
	{
		$oQuery = $this->meta()->db()->query("
			SELECT	TOP 1 c.TABLE_SCHEMA AS [schema], c.TABLE_NAME AS [table], COLUMN_NAME AS name,
					c.ORDINAL_POSITION AS num, c.COLUMN_DEFAULT AS [default], c.IS_NULLABLE AS nullable,
					c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS max_length, CAST(p.value AS varchar) AS comment
			FROM	INFORMATION_SCHEMA.COLUMNS c LEFT JOIN sys.extended_properties p
						ON	p.major_id	= OBJECT_ID(QUOTENAME(c.TABLE_SCHEMA) + N'.' + QUOTENAME(c.TABLE_NAME))
						AND	p.minor_id	= c.ORDINAL_POSITION
						AND	p.class		= 1 -- c.class_desc = N'COLUMN_OR_OBJECT'
						AND	p.name		= N'MS_Description'
			WHERE	c.COLUMN_NAME	= :column
				AND	c.TABLE_NAME	= :name
				AND	c.TABLE_SCHEMA	= :schema
		", array('column' => $sName) + $this->aData);

		count($oQuery) == 1 or burn('UnexpectedValueException',
			sprintf(_WT('Column "%s" does not exist.'), $sName));

		return $this->instantiateObject($this->getColumnClass(), $oQuery->fetch());
	}

	/**
		Return whether a given column exists in the table.

		@param	$sName	The column name.
		@return	bool	true if the column exists, false otherwise.
	*/

	public function columnExists($sName)
	{
		return (bool)$this->db()->queryValue('
			SELECT		TOP 1 COUNT(*)
				FROM	INFORMATION_SCHEMA.COLUMNS
				WHERE	COLUMN_NAME		= :column
					AND	TABLE_NAME		= :name
					AND	TABLE_SCHEMA	= :schema
		', array('column' => $sName) + $this->aData);
	}

	/**
		Returns the comment of the table.

		@return string The comment.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Return a foreign key of a given name.

		@param	$sName	The name of the foreign key.
		@return	weeMSSQLDbMetaForeignKey	The foreign key.
		@throw	UnexpectedValueException	The foreign key does not exist.
	*/

	public function foreignKey($sName)
	{
		$oQuery = $this->db()->query("
			SELECT	 	TOP 1 cc.CONSTRAINT_SCHEMA AS [schema], cc.TABLE_NAME AS [table], cc.CONSTRAINT_NAME AS name,
						rc.UNIQUE_CONSTRAINT_SCHEMA AS referenced_schema, ft.TABLE_NAME AS referenced_table,
						rc.UNIQUE_CONSTRAINT_NAME AS referenced_constraint
				FROM 	INFORMATION_SCHEMA.TABLE_CONSTRAINTS cc
							JOIN INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS rc
					 			ON rc.CONSTRAINT_SCHEMA = cc.CONSTRAINT_SCHEMA AND rc.CONSTRAINT_NAME = cc.CONSTRAINT_NAME
							JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS ft
								ON ft.CONSTRAINT_SCHEMA = rc.UNIQUE_CONSTRAINT_SCHEMA AND ft.CONSTRAINT_NAME = rc.UNIQUE_CONSTRAINT_NAME
				WHERE	cc.TABLE_SCHEMA = :schema AND cc.TABLE_NAME = :name AND cc.CONSTRAINT_NAME = :constraint
		", array('constraint' => $sName) + $this->aData);

		count($oQuery) == 1
			or burn('UnexpectedValueException',
				sprintf(_WT('Foreign key "%s" does not exist.'), $sName));

		return $this->instantiateObject($this->getForeignKeyClass(), $oQuery->fetch());
	}

	/**
		Returns whether a foreign key of a given name exists.

		@param	$sName	The name of the table.
		@return	bool	Whether the foreign key exists.
	*/

	public function foreignKeyExists($sName)
	{
		return (bool)$this->db()->queryValue("
			SELECT		TOP 1 COUNT(*)
				FROM	INFORMATION_SCHEMA.TABLE_CONSTRAINTS
				WHERE	TABLE_SCHEMA	= :schema
					AND	TABLE_NAME		= :name
					AND	CONSTRAINT_NAME	= :constraint
					AND	CONSTRAINT_TYPE	= 'FOREIGN KEY'
		", array('constraint' => $sName) + $this->aData);
	}

	/**
		Return all the foreign keys.

		@return	array(weeMSSQLDbMetaForeignKey)	The array of foreign keys.
	*/

	public function foreignKeys()
	{
		$oQuery = $this->db()->query("
			SELECT	 		cc.CONSTRAINT_SCHEMA AS [schema], cc.TABLE_NAME AS [table], cc.CONSTRAINT_NAME AS name,
					 		rc.UNIQUE_CONSTRAINT_SCHEMA AS referenced_schema, ft.TABLE_NAME AS referenced_table,
					 		rc.UNIQUE_CONSTRAINT_NAME AS referenced_constraint
				FROM 		INFORMATION_SCHEMA.TABLE_CONSTRAINTS cc
					 			JOIN INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS rc
					 				ON rc.CONSTRAINT_SCHEMA = cc.CONSTRAINT_SCHEMA AND rc.CONSTRAINT_NAME = cc.CONSTRAINT_NAME
					 			JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS ft
					 				ON ft.CONSTRAINT_SCHEMA = rc.UNIQUE_CONSTRAINT_SCHEMA AND ft.CONSTRAINT_NAME = rc.UNIQUE_CONSTRAINT_NAME
				WHERE		cc.TABLE_SCHEMA = :schema AND cc.TABLE_NAME = :name
				ORDER BY	cc.CONSTRAINT_NAME
		", $this->aData);

		$aForeignKeys	= array();
		$sClass			= $this->getForeignKeyClass();
		foreach ($oQuery as $aForeignKey)
			$aForeignKeys[] = $this->instantiateObject($sClass, $aForeignKey);
		return $aForeignKeys;
	}

	/**
		Return the name of the column class.

		@return	string	The name of the column class.
	*/

	public function getColumnClass()
	{
		return 'weeMSSQLDbMetaColumn';
	}

	/**
		Return the name of the foreign key class.

		@return	string	The name of the foreign key class.
	*/

	public function getForeignKeyClass()
	{
		return 'weeMSSQLDbMetaForeignKey';
	}

	/**
		Return the name of the primary key class.

		@return	string	The name of the primary key class.
	*/

	public function getPrimaryKeyClass()
	{
		return 'weeMSSQLDbMetaPrimaryKey';
	}

	/**
		Return whether the table has a primary key.

		@return	bool	true if the table has a primary key, false otherwise.
	*/

	public function hasPrimaryKey()
	{
		return (bool)$this->db()->queryValue("
			SELECT		TOP 1 COUNT(*)
				FROM	INFORMATION_SCHEMA.TABLE_CONSTRAINTS
				WHERE	TABLE_NAME		= :name
					AND	CONSTRAINT_TYPE	= 'PRIMARY KEY'
					AND	TABLE_SCHEMA	= :schema
		", $this->aData);
	}

	/**
		Return the primary key of the table.

		@return	weeMSSQLDbMetaPrimaryKey	The primary key of the table.
		@throw	IllegalStateException		The table does not have a primary key.
	*/

	public function primaryKey()
	{
		$oQuery = $this->db()->query("
			SELECT		TOP 1 CONSTRAINT_SCHEMA AS [schema], TABLE_NAME AS [table], CONSTRAINT_NAME AS name
				FROM	INFORMATION_SCHEMA.TABLE_CONSTRAINTS
				WHERE	TABLE_NAME		= :name
					AND	TABLE_SCHEMA	= :schema
					AND	CONSTRAINT_TYPE	= 'PRIMARY KEY'
		", $this->aData);

		count($oQuery) == 1 or burn('IllegalStateException',
			_WT('The table does not have a primary key.'));

		return $this->instantiateObject($this->getPrimaryKeyClass(), $oQuery->fetch());
	}

	/**
		Query all the columns of the table.

		@return	weeMSSQLResult	The data of all the columns of the table.
	*/

	protected function queryColumns()
	{
		return $this->meta()->db()->query("
			SELECT		c.TABLE_SCHEMA AS [schema], c.TABLE_NAME AS [table], COLUMN_NAME AS name,
						c.ORDINAL_POSITION AS num, c.COLUMN_DEFAULT AS [default], c.IS_NULLABLE AS nullable,
						c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS max_length, CAST(p.value AS varchar) AS comment
			FROM		INFORMATION_SCHEMA.COLUMNS c LEFT JOIN sys.extended_properties p
							ON	p.major_id	= OBJECT_ID(QUOTENAME(c.TABLE_SCHEMA) + N'.' + QUOTENAME(c.TABLE_NAME))
							AND	p.minor_id	= c.ORDINAL_POSITION
							AND	p.class		= 1 -- c.class_desc = N'COLUMN_OR_OBJECT'
							AND	p.name		= N'MS_Description'
			WHERE		c.TABLE_NAME	= :name
					AND	c.TABLE_SCHEMA	= :schema
			ORDER BY	c.ORDINAL_POSITION
		", $this->aData);
	}

	/**
		Return the name of the schema of the table.

		@return	string	The name of the schema.
	*/

	public function schemaName()
	{
		return $this->aData['schema'];
	}
}
