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
	PostgreSQL specialization of weeDbMetaPrimaryKey.
*/

class weePgSQLDbMetaPrimaryKey extends weeDbMetaPrimaryKey
	implements weeDbMetaCommentable, weeDbMetaSchemaObject
{
	/**
		Initializes a new pgsql primary key object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weePgSQLDbMetaTable.

		@param	$oMeta			The pgsql dbmeta object.
		@param	$aData			The primary key data.
		@param	$oTable			The pgsql table of the primary key.
	*/

	public function __construct(weePgSQLDbMeta $oMeta, array $aData, weePgSQLDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData, $oTable);
	}

	/**
		Returns the columns of the table constraint.

		The columns are ordered as specified in the definition of the primary key.

		@return	array(string)	The names of the columns of the constraint.
	*/

	public function columns()
	{
		$oQuery = $this->db()->query("
			SELECT			attname
				FROM		pg_catalog.pg_attribute
				WHERE		attrelid = CAST(? AS regclass)
				ORDER BY	attnum
		", $this->quotedName());

		$aColumns = array();
		foreach ($oQuery as $aColumn)
			$aColumns[] = $aColumn['attname'];
		return $aColumns;
	}

	/**
		Returns the comment of the primary key.

		@return	string			The comment of the primary key.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Returns the name of the schema of the column.

		@return	string			The name of the schema.
	*/

	public function schemaName()
	{
		return $this->aData['schema'];
	}
}
