<?php

/*
	Web:Extend
	Copyright (c) 2006 Dev:Extend

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
	Class used to query meta informations about schemas and their objects.
*/

class weeDbMetaSchema extends weeDbMetaObject
{
	/**
		Returns the array of fields which need to be passed to the constructor of the class.

		@return	array	The array of fields.
	*/

	public static function getFields()
	{
		return array('schema_name');
	}

	/**
		Returns the array of fields used to order the objects in the SQL SELECT query.

		@return	array	The array of order fields.
	*/

	public static function getOrderFields()
	{
		self::getFields();
	}

	/**
		Returns the name of the information_schema table where the column objects
		are stored.

		@return	string	The table name.
	*/

	public static function getTable()
	{
		return 'information_schema.schemas';
	}

	/**
		Returns the name of the schema.
		
		@return string	The name of the schema.
	*/

	public function name()
	{
		return $this->aInfos['schema_name'];
	}

	/**
		Returns a table of a given name in the schema.

		@param	$sName				The name of the table.
		@throw	DatabaseException	The table does not exist in the schema.
		@return weeDbMetaTable		The table.
	*/

	public function table($sName)
	{
		return $this->oMeta->table($this->name() . '.' . $sName);
	}

	/**
		Returns all the tables of the schema.

		@return array	The array of tables.
	*/

	public function tables()
	{
		return $this->oMeta->tables($this->name());
	}
}
