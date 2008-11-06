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
	PostgreSQL specialization of weeDbMetaColumn.
*/

class weePgSQLDbMetaColumn extends weeDbMetaColumn 
	implements weeDbMetaCommentable, weeDbMetaSchemaObject
{
	/**
		Initializes a new pgsql column object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weePgSQLDbMetaTable.

		@param	$oMeta					The pgsql dbmeta object.
		@param	$aData					The column data.
		@param	$oTable					The pgsql table of the column.
	*/

	public function __construct(weePgSQLDbMeta $oMeta, array $aData, weePgSQLDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData, $oTable);
	}

	/**
		Returns the comment of the column.

		@return	string					The comment of the column.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Returns the default value of the column.

		@return	string					The default value of the column.
		@throw	IllegalStateException	The column does not have a default value.
	*/

	public function defaultValue()
	{
		$this->hasDefault()
			or burn('IllegalStateException',
				_WT('The column does not have a default value.'));

		return $this->db()->queryValue('
			SELECT		pg_catalog.pg_get_expr(adbin, adrelid)
				FROM	pg_catalog.pg_attrdef
				WHERE	adrelid	= ?::regclass
					AND	adnum	= ?
		', $this->oTable->quotedName(), $this->num());
	}

	/**
		Returns whether the column has a default value.

		@return	bool					true if the column has a default value, false otherwise.
	*/

	public function hasDefault()
	{
		return $this->aData['has_default'] == 't';
	}

	/**
		Returns whether the column can contain null values.
	
		@return	bool					true if the column accepts null as a value, false otherwise.
	*/

	public function isNullable()
	{
		return $this->aData['nullable'] == 't';
	}

	/**
		Returns the number of the column in the table.

		@return	int						The number of the column in the table.
	*/

	public function num()
	{
		return (int)$this->aData['num'];
	}

	/**
		Returns the name of the schema of the column.

		@return	string					The name of the schema.
	*/

	public function schemaName()
	{
		return $this->aData['schema'];
	}
}
