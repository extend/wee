<?php

/*
	Web:Extend
	Copyright (c) 2008 Dev:Extend

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

class weeMySQLDbMetaTable extends weeDbMetaTable implements weeDbMetaCommentable
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
						COLUMN_DEFAULT AS `default`, IS_NULLABLE AS nullable, COLUMN_COMMENT AS comment
				FROM	information_schema.columns
				WHERE	COLUMN_NAME		= ?
					AND	TABLE_NAME		= ?
					AND	TABLE_SCHEMA	= DATABASE()
				LIMIT	1
		', $sName, $this->name());

		count($oQuery) == 1
			or burn('UnexpectedValueException',
				sprintf(_('Column "%s" does not exist.'), $sName));

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
		Returns all the columns of the table.

		@return	array(weeMySQLDbMetaColumn)	The array of tables.
	*/

	public function columns()
	{
		$oQuery = $this->meta()->db()->query('
			SELECT			TABLE_NAME AS `table`, COLUMN_NAME AS name, ORDINAL_POSITION AS num,
							COLUMN_DEFAULT AS `default`, IS_NULLABLE AS nullable, COLUMN_COMMENT AS comment
				FROM		information_schema.columns
				WHERE		TABLE_NAME		= :name
						AND	TABLE_SCHEMA	= DATABASE()
				ORDER BY	ORDINAL_POSITION
		', $this->aData);

		$aColumns = array();
		foreach ($oQuery as $aColumn)
			$aColumns[] = $this->instantiateObject($this->getColumnClass(), $aColumn);
		return $aColumns;
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
		Returns the name of the column class.

		@return	string						The name of the column class.
	*/

	public function getColumnClass()
	{
		return 'weeMySQLDbMetaColumn';
	}

	/**
		Returns the name of the primary key class.

		@return	string					The name of the primary key class.
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
				_('The table does not have a primary key.'));

		return $this->instantiateObject($this->getPrimaryKeyClass(), $oQuery->fetch());
	}
}
