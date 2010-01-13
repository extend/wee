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
	Class used to query meta data about schemas and their objects.
*/

abstract class weeDbMetaSchema extends weeDbMetaObject
	implements weeDbMetaTableProvider
{
	/**
		Returns all the tables in the schema.

		@return	array(weeDbMetaTable)	The array of tables.
	*/

	public function tables()
	{
		$oMeta		= $this->meta();
		$aTables	= array();
		$sClass		= $oMeta->getTableClass();
		foreach ($this->queryTables() as $aTable)
			$aTables[] = new $sClass($oMeta, $aTable);
		return $aTables;
	}

	/**
		Returns the names of all the tables in the schema.

		@return	array(string)	The names of all the tables.
	*/

	public function tablesNames()
	{
		$aTables	= array();
		$sClass		= $this->meta()->getTableClass();
		foreach ($this->queryTables() as $aTable)
			$aTables[] = $aTable['name'];
		return $aTables;
	}

	/**
		Queries all the tables in the schema.

		@return	weeDatabaseResult	The data of all the tables in the schema.
	*/

	abstract protected function queryTables();
}
