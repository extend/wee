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
	Interface implemented by dbmeta objects which driver supports schemas.
*/

interface weeDbMetaSchemaProvider
{
	/**
		Returns the current schema of the database.

		@return	weeDbMetaSchema			The current schema.
	*/

	public function currentSchema();

	/**
		Returns the name of the schema class.

		@return	string					The name of the schema class.
	*/

	public function getSchemaClass();

	/**
		Returns a schema of a given name in the database.

		@param	$sName					The name of the schema.
		@return	weeDbMetaSchema			The schema.
	*/

	public function schema($sName);

	/**
		Returns whether a schema of a given name exists in the database.

		@param	$sName					The name of the schema.
		@return	bool					true if the schema exists in the database, false otherwise.
	*/

	public function schemaExists($sName);

	/**
		Returns all the schemas of the database.

		@return	array(weeDbMetaSchema)	The array of schemas.
	*/

	public function schemas();
}
