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
	MySQL specialization of weeDbMetaTable.
*/

class weeMySQLDbMetaTable extends weeDbMetaTable
	implements weeDbMetaCommentable, weeDbMetaForeignKeyProvider
{
	/**
		Initializes a new mysql table object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeMySQLDbMeta.

		@param	$oMeta						The mysql dbmeta object.
		@param	$aData						The object data.
	*/

	public function __construct(weeMySQLDbMeta $oMeta, array $aData)
	{
		parent::__construct($oMeta, $aData);
	}

	/**
		Returns a column of the table.

		@param	$sName						The column name.
		@return	weeMySQLDbMetaColumn		The column.
	*/

	public function column($sName)
	{
		$oQuery = $this->meta()->db()->query('
			SELECT		TABLE_NAME AS `table`, COLUMN_NAME AS name, ORDINAL_POSITION AS num,
						COLUMN_DEFAULT AS `default`, IS_NULLABLE AS nullable, COLUMN_COMMENT AS comment,
						DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS max_length,
						COLUMN_TYPE AS raw_type
				FROM	information_schema.columns
				WHERE	COLUMN_NAME		= ?
					AND	TABLE_NAME		= ?
					AND	TABLE_SCHEMA	= DATABASE()
				LIMIT	1
		', $sName, $this->name());

		count($oQuery) == 1
			or burn('UnexpectedValueException',
				sprintf(_WT('Column "%s" does not exist.'), $sName));

		return $this->instantiateObject($this->getColumnClass(), $oQuery->fetch());
	}

	/**
		Returns whether a given column exists in the table.

		@param	$sName						The column name.
		@return	bool						true if the column exists, false otherwise.
	*/

	public function columnExists($sName)
	{
		return (bool)$this->db()->queryValue('SELECT EXISTS(
			SELECT 1
				FROM	information_schema.columns
				WHERE	COLUMN_NAME		= ?
					AND	TABLE_NAME		= ?
					AND	TABLE_SCHEMA	= DATABASE()
		)', $sName, $this->name());
	}

	/**
		Returns the comment of the table.

		@return	string						The comment of the table.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Returns a foreign key of a given name.

		@param	$sName						The name of the foreign key.
		@return	weeMySQLDbMetaPrimaryKey	The foreign key.
		@throw	UnexpectedValueException	The foreign key does not exist.
	*/

	public function foreignKey($sName)
	{
		$oQuery = $this->db()->query("
			SELECT		TABLE_NAME AS `table`, CONSTRAINT_NAME AS name
				FROM	information_schema.TABLE_CONSTRAINTS
				WHERE	CONSTRAINT_NAME		= ?
					AND	TABLE_NAME			= ?
					AND	CONSTRAINT_TYPE		= 'FOREIGN KEY'
					AND	CONSTRAINT_SCHEMA	= DATABASE()
				LIMIT	1
		", $sName, $this->name());

		count($oQuery) == 1
			or burn('UnexpectedValueException',
				sprintf(_WT('Foreign key "%s" does not exist.'), $sName));

		return $this->instantiateObject($this->getForeignKeyClass(), $oQuery->fetch());
	}

	/**
		Returns whether a foreign key of a given name exists.

		@param	$sName						The name of the table.
		@return	bool						Whether the foreign key exists.
	*/

	public function foreignKeyExists($sName)
	{
		return (bool)$this->db()->queryValue("SELECT EXISTS(
			SELECT 1
				FROM	information_schema.TABLE_CONSTRAINTS
				WHERE	CONSTRAINT_NAME		= ?
					AND	TABLE_NAME			= ?
					AND	CONSTRAINT_TYPE		= 'FOREIGN KEY'
					AND	CONSTRAINT_SCHEMA	= DATABASE()
		)", $sName, $this->name());
	}

	/**
		Returns all the foreign keys.

		@return	array(weeMySQLDbMetaPrimaryKey)	The array of foreign keys.
	*/

	public function foreignKeys()
	{
		$oQuery = $this->db()->query('
			SELECT			TABLE_NAME AS table, CONSTRAINT_NAME AS name,
							REFERENCED_TABLE_NAME AS referenced_table,
							UNIQUE_CONSTRAINT_NAME AS referenced_constraint
				FROM		information_schema.REFERENTIAL_CONSTRAINTS
				WHERE		TABLE_NAME			= ?
						AND	CONSTRAINT_SCHEMA	= DATABASE()
				ORDER BY	CONSTRAINT_NAME
		', $this->name());

		$aForeignKeys	= array();
		$sClass			= $this->getForeignKeyClass();
		foreach ($oQuery as $aForeignKey)
			$aForeignKeys[] = $this->instantiateObject($sClass, $aForeignKey);
		return $aForeignKeys;
	}

	/**
		Returns the name of the column class.

		@return	string						The name of the column class.
	*/

	public function getColumnClass()
	{
		return 'weeMySQLDbMetaColumn';
	}

	/**
		Returns the name of the foreign key class.

		@return	string						The name of the foreign key class.
	*/

	public function getForeignKeyClass()
	{
		return 'weeMySQLDbMetaForeignKey';
	}

	/**
		Returns the name of the primary key class.

		@return	string						The name of the primary key class.
	*/

	public function getPrimaryKeyClass()
	{
		return 'weeMySQLDbMetaPrimaryKey';
	}

	/**
		Returns whether the table has a primary key.

		@return	bool						true if the table has a primary key, false otherwise.
	*/

	public function hasPrimaryKey()
	{
		return (bool)$this->db()->queryValue("SELECT EXISTS(
			SELECT 1
				FROM	information_schema.table_constraints
				WHERE	TABLE_NAME		= ?
					AND	CONSTRAINT_TYPE	= 'PRIMARY KEY'
					AND	TABLE_SCHEMA	= DATABASE()
		)", $this->name());
	}

	/**
		Returns the primary key of the table.

		@return	weeMySQLDbMetaPrimaryKey	The primary key of the table.
		@throw	IllegalStateException		The table does not have a primary key.
	*/

	public function primaryKey()
	{
		$oQuery = $this->db()->query("
			SELECT		CONSTRAINT_NAME AS name, CONSTRAINT_TYPE AS type
				FROM	information_schema.TABLE_CONSTRAINTS
				WHERE	TABLE_NAME		= ?
					AND	TABLE_SCHEMA	= DATABASE()
					AND	CONSTRAINT_TYPE	= 'PRIMARY KEY'
				LIMIT	1
		", $this->name());

		count($oQuery) == 1
			or burn('IllegalStateException',
				_WT('The table does not have a primary key.'));

		return $this->instantiateObject($this->getPrimaryKeyClass(), $oQuery->fetch());
	}

	/**
		Queries all the columns of the table.

		@return	weeMySQLResult				The data of all the columns of the table.
	*/

	protected function queryColumns()
	{
		return $this->meta()->db()->query('
			SELECT			TABLE_NAME AS `table`, COLUMN_NAME AS name, ORDINAL_POSITION AS num,
							COLUMN_DEFAULT AS `default`, IS_NULLABLE AS nullable, COLUMN_COMMENT AS comment,
							DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS max_length,
							COLUMN_TYPE AS raw_type
				FROM		information_schema.columns
				WHERE		TABLE_NAME		= :name
						AND	TABLE_SCHEMA	= DATABASE()
				ORDER BY	ORDINAL_POSITION
		', $this->aData);
	}
}
