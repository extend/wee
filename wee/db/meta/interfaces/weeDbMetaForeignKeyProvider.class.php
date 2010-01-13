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
	Interface implemented by dbmeta tables which driver supports foreign keys.
*/

interface weeDbMetaForeignKeyProvider
{
	/**
		Returns the name of the foreign key class.

		@return	string					The name of the foreign key class.
	*/

	public function getForeignKeyClass();

	/**
		Returns a foreign key of a given name.

		@param	$sName						The name of the foreign key.
		@return	weeDbMetaPrimaryKey			The foreign key.
	*/

	public function foreignKey($sName);

	/**
		Returns whether a foreign key of a given name exists.

		@param	$sName						The name of the table.
		@return	bool						Whether the foreign key exists.
	*/

	public function foreignKeyExists($sName);

	/**
		Returns all the foreign keys.

		@return	array(weeDbMetaPrimaryKey)	The array of foreign keys.
	*/

	public function foreignKeys();
}
