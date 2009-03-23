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
	MSSQL specialization of weeDbMetaSchema.
*/

class weeMSSQLDbMetaSchema extends weeDbMetaSchema
	implements weeDbMetaTableProvider
{
	/**
		Initialize a new mssql schema object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeMSSQLDbMeta.

		@param	$oMeta	The mssql dbmeta object.
		@param	$aData	The schema data.
	*/

	public function __construct(weeMSSQLDbMeta $oMeta, array $aData)
	{
		parent::__construct($oMeta, $aData);
	}

	/**
		Returns the comment of the schema.

		@return string The comment.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Return a table of a given name in the schema.

		@param	$sName	The name of the table.
		@return	weeMSSQLDbMetaTable			The table.
		@throw	UnexpectedValueException	The table does not exist in the schema.
	*/

	public function table($sName)
	{
		return $this->meta()->tableInSchema($sName, $this->name());
	}

	/**
		Return whether a table of a given name exists in the schema.

		@param	$sName	The name of the table.
		@return	bool	true if the table exists in the schema, false otherwise.
	*/

	public function tableExists($sName)
	{
		return $this->meta()->tableExistsInSchema($sName, $this->name());
	}

	/**
		Query all the tables in the schema.

		@return	weeMSSQLResult	The data of all the tables in the schema.
	*/

	protected function queryTables()
	{
		return $this->meta()->queryTablesInSchema($this->name());
	}
}
