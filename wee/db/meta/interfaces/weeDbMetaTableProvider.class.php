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
	Interface implemented by dbmeta objects which driver supports tables.
*/

interface weeDbMetaTableProvider
{
	/**
		Returns a table of a given name in the database.

		@param	$sName					The name of the table.
		@return	weeDbMetaTable			The table.
	*/

	public function table($sName);

	/**
		Returns whether a table of a given name exists in the database.

		@param	$sName					The name of the table.
		@return	bool					true if the table exists in the database, false otherwise.
	*/

	public function tableExists($sName);

	/**
		Returns all the tables of the database.

		@return	array(weeDbMetaTable)	The array of tables.
	*/

	public function tables();
}
