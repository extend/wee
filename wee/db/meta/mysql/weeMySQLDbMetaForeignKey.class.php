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
	MySQL specialization of weeDbMetaForeignKey.
*/

class weeMySQLDbMetaForeignKey extends weeDbMetaForeignKey
{
	/**
		Initializes a new mysql primary key object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeMySQLDbMetaTable.

		@param	$oMeta			The mysql dbmeta object.
		@param	$aData			The primary key data.
		@param	$oTable			The mysql table of the primary key.
	*/

	public function __construct(weeMySQLDbMeta $oMeta, array $aData, weeMySQLDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData, $oTable);
	}

	/**
		Returns the columns of the foreign key.

		@return	array(string)	The names of the columns of the foreign key.
	*/

	public function columnsNames()
	{
		$oQuery = $this->db()->query('
			SELECT			COLUMN_NAME
				FROM		information_schema.KEY_COLUMN_USAGE
				WHERE		CONSTRAINT_NAME		= ?
						AND	TABLE_NAME			= ?
						AND	CONSTRAINT_SCHEMA	= DATABASE()
				ORDER BY	ORDINAL_POSITION
		', $this->name(), $this->tableName());

		$aNames = array();
		foreach ($oQuery as $aColumn)
			$aNames[] = $aColumn['COLUMN_NAME'];
		return $aNames;
	}

	/**
		Returns the referenced columns of the foreign key.

		@return	array(string)	The names of the referenced columns of the foreign key.
	*/

	public function referencedColumnsNames()
	{
		$oQuery = $this->db()->query('
			SELECT			REFERENCED_COLUMN_NAME
				FROM		information_schema.KEY_COLUMN_USAGE
				WHERE		CONSTRAINT_NAME		= ?
						AND	TABLE_NAME			= ?
						AND	CONSTRAINT_SCHEMA	= DATABASE()
				ORDER BY	ORDINAL_POSITION
		', $this->name(), $this->tableName());

		$aNames = array();
		foreach ($oQuery as $aColumn)
			$aNames[] = $aColumn['REFERENCED_COLUMN_NAME'];
		return $aNames;
	}

	/**
		Returns the name of the referenced table of the foreign key.

		@return	string			The name of the referenced table of the foreign key.
	*/

	public function referencedTableName()
	{
		return $this->db()->queryValue('
			SELECT		REFERENCED_TABLE_NAME
				FROM	information_schema.KEY_COLUMN_USAGE
				WHERE	CONSTRAINT_NAME		= ?
					AND	TABLE_NAME			= ?
					AND	CONSTRAINT_SCHEMA	= DATABASE()
				LIMIT	1
		', $this->name(), $this->tableName());
	}
}
