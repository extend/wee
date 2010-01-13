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
	Oracle specialisation of weeDbMetaTable.
*/

class weeOracleDbMetaTable extends weeDbMetaTable
	implements weeDbMetaCommentable, weeDbMetaForeignKeyProvider, weeDbMetaSchemaObject
{
	/**
		Initialise a new oracle table object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeOracleDbMeta.

		@param	$oMeta	The oracle dbmeta object.
		@param	$aData	The object data.
	*/

	public function __construct(weeOracleDbMeta $oMeta, array $aData)
	{
		parent::__construct($oMeta, $aData);
	}

	/**
		Return a column of the table.

		@param	$sName	The column name.
		@return	weeOracleDbMetaColumn		The column.
		@throw	UnexpectedValueException	The column does not exist.
	*/

	public function column($sName)
	{
		$oQuery = $this->meta()->db()->query('
			SELECT	OWNER AS "schema", TABLE_NAME AS "table", COLUMN_NAME AS "name", tc.NULLABLE AS "nullable",
					tc.COLUMN_ID AS "num", tc.DATA_DEFAULT AS "default", cc.COMMENTS AS "comment",
					tc.DATA_TYPE_OWNER AS "type_schema", DATA_TYPE AS "type", DATA_TYPE_MOD AS "type_mod",
					tc.DATA_SCALE AS "data_scale", tc.CHAR_LENGTH AS "char_length"
			FROM	SYS.ALL_TAB_COLUMNS tc LEFT JOIN SYS.ALL_COL_COMMENTS cc USING (OWNER, TABLE_NAME, COLUMN_NAME)
			WHERE	OWNER = :schema AND TABLE_NAME = :name AND COLUMN_NAME = :column
		', array('column' => $sName) + $this->aData);

		count($oQuery) == 1 or burn('UnexpectedValueException',
			sprintf(_WT('Column "%s" does not exist.'), $sName));

		return $this->instantiateObject($this->getColumnClass(), $oQuery->fetch());
	}

	/**
		Returns whether a given column exists in the table.

		@param	$sName	The column name.
		@return	bool	Whether a given column exists in the table.
	*/

	public function columnExists($sName)
	{
		return (bool)$this->db()->queryValue('
			SELECT	COUNT(*)
			FROM	SYS.ALL_TAB_COLUMNS
			WHERE	OWNER = :schema AND TABLE_NAME = :name AND COLUMN_NAME = :column AND ROWNUM = 1
		', array('column' => $sName) + $this->aData);
	}

	/**
		Returns the comment of the table.

		@return	string	The comment of the table.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Return a foreign key of a given name.

		@param	$sName	The name of the foreign key.
		@return	weeOracleDbMetaForeignKey	The foreign key.
		@throw	UnexpectedValueException	The foreign key does not exist.
	*/

	public function foreignKey($sName)
	{
		$oQuery = $this->db()->query('
			SELECT	c.OWNER AS "schema", c.TABLE_NAME AS "table", c.CONSTRAINT_NAME AS "name",
					c.R_OWNER AS "referenced_schema", r.TABLE_NAME AS "referenced_table",
					c.R_CONSTRAINT_NAME AS "referenced_constraint"
			FROM	SYS.ALL_CONSTRAINTS c JOIN SYS.ALL_CONSTRAINTS r
						ON c.R_OWNER = r.OWNER AND c.R_CONSTRAINT_NAME = r.CONSTRAINT_NAME
			WHERE	c.OWNER = :schema AND c.TABLE_NAME = :name AND c.CONSTRAINT_NAME = :fk AND c.CONSTRAINT_TYPE = \'R\'
		', array('fk' => $sName) + $this->aData);

		count($oQuery) == 1 or burn('UnexpectedValueException',
			sprintf(_WT('Foreign key "%s" does not exist.'), $sName));

		return $this->instantiateObject($this->getForeignKeyClass(), $oQuery->fetch());
	}

	/**
		Return whether a foreign key of a given name exists.

		@param	$sName	The name of the table.
		@return	bool	Whether the foreign key exists.
	*/

	public function foreignKeyExists($sName)
	{
		return (bool)$this->db()->queryValue("
			SELECT	COUNT(*)
			FROM	SYS.ALL_CONSTRAINTS
			WHERE	OWNER = :schema AND TABLE_NAME = :name AND CONSTRAINT_NAME = :fk AND CONSTRAINT_TYPE = 'R' AND ROWNUM = 1
		", array('fk' => $sName) + $this->aData);
	}

	/**
		Return all the foreign keys.

		@return	array(weeOracleDbMetaForeignKey)	The array of foreign keys.
	*/

	public function foreignKeys()
	{
		$oQuery = $this->db()->query('
			SELECT		c.OWNER AS "schema", c.TABLE_NAME AS "table", c.CONSTRAINT_NAME AS "name",
						c.R_OWNER AS "referenced_schema", r.TABLE_NAME AS "referenced_table",
						c.R_CONSTRAINT_NAME AS "referenced_constraint"
			FROM		SYS.ALL_CONSTRAINTS c JOIN SYS.ALL_CONSTRAINTS r
							ON c.R_OWNER = r.OWNER AND c.R_CONSTRAINT_NAME = r.CONSTRAINT_NAME
			WHERE		c.OWNER = :schema AND c.TABLE_NAME = :name AND c.CONSTRAINT_TYPE = \'R\'
			ORDER BY	c.CONSTRAINT_NAME
		', $this->aData);

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
		return 'weeOracleDbMetaColumn';
	}

	/**
		Return the name of the foreign key class.

		@return	string	The name of the foreign key class.
	*/

	public function getForeignKeyClass()
	{
		return 'weeOracleDbMetaForeignKey';
	}

	/**
		Return the name of the primary key class.

		@return	string	The name of the primary key class.
	*/

	public function getPrimaryKeyClass()
	{
		return 'weeOracleDbMetaPrimaryKey';
	}

	/**
		Returns whether the table has a primary key.

		@return	bool	true if the table has a primary key, false otherwise.
	*/

	public function hasPrimaryKey()
	{
		return (bool)$this->db()->queryValue("
			SELECT	COUNT(*)
			FROM	SYS.ALL_CONSTRAINTS
			WHERE	OWNER = :schema AND TABLE_NAME = :name AND CONSTRAINT_TYPE = 'P' AND ROWNUM = 1
		", $this->aData);
	}

	/**
		Returns the primary key of the table.

		@return	weeOracleDbMetaPrimaryKey	The primary key of the table.
		@throw	IllegalStateException		The table does not have a primary key.
	*/

	public function primaryKey()
	{
		$oQuery = $this->db()->query('
			SELECT		c.OWNER AS "schema", c.TABLE_NAME AS "table", c.CONSTRAINT_NAME AS "name"
			FROM		SYS.ALL_CONSTRAINTS c
			WHERE		c.OWNER = :schema AND c.TABLE_NAME = :name AND c.CONSTRAINT_TYPE = \'P\'
		', $this->aData);

		count($oQuery) == 1 or burn('IllegalStateException',
			_WT('The table does not have a primary key.'));

		return $this->instantiateObject($this->getPrimaryKeyClass(), $oQuery->fetch());
	}

	/**
		Query all the columns of the table.

		@return	weeOracleResult	The data of all the columns of the table.
	*/

	protected function queryColumns()
	{
		return $this->meta()->db()->query('
			SELECT		OWNER AS "schema", TABLE_NAME AS "table", COLUMN_NAME AS "name", tc.NULLABLE AS "nullable",
						tc.COLUMN_ID AS "num", tc.DATA_DEFAULT AS "default", cc.COMMENTS AS "comment",
						tc.DATA_TYPE_OWNER AS "type_schema", tc.DATA_TYPE AS "type", tc.DATA_TYPE_MOD AS "type_mod",
						tc.DATA_SCALE AS "data_scale", tc.CHAR_LENGTH AS "char_length"
			FROM		SYS.ALL_TAB_COLUMNS tc LEFT JOIN SYS.ALL_COL_COMMENTS cc USING (OWNER, TABLE_NAME, COLUMN_NAME)
			WHERE		OWNER = :schema AND TABLE_NAME = :name
			ORDER BY	COLUMN_ID
		', $this->aData);
	}

	/**
		Return the name of the schema of the table.

		@return	string	The name of the schema of the table.
	*/

	public function schemaName()
	{
		return $this->aData['schema'];
	}
}
