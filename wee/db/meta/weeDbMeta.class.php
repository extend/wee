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
	Class used to query meta informations about databases and their objects.

	This class makes use of the SQL INFORMATION_SCHEMA standard schema to do its job,
	any SQL-compliant DBMS which implements this schema should work with it.
*/

class weeDbMeta
{
	/**
		The database to query.
	*/

	protected $oDb;

	/**
		Initializes a new database meta.

		@param $oDb The database to query.
	*/

	public function __construct(weeDatabase $oDb)
	{
		fire($oDb == null, 'UnexpectedValueException',
			'$oDb is null.');

		$this->oDb = $oDb;
	}

	/**
		Build a SQL WHERE clause from an object name and an array of fields.

		For example, given those parameters:
			$sName = 'bbs.forum';
			$aFields = array('table_space', 'table_name');

		The method will return the following WHERE clause:
			table_space = 'bbs' AND table_name = 'forum'

		If the number of parts in the name is smaller than the number of fields,
		priority is given to the LAST fields.
		
		With the same fields array, given this name:
			$sName = 'forum';

		The method will return the following WHERE clause:
			table_name = 'forum'

		If the number of parts in the name exceeds the size of the array of fields,
		an UnexpectedValueException is thrown.

		@param	$sName		The object name.
		@param	$aFields	The array of fields.
		@return string		The SQL WHERE clause built from the name and the array of fields.
	*/

	protected static function buildWhereClause($sName, $aFields)
	{
		$aFilters	= array();
		$i			= strlen($sName);

		fire(substr_count($sName, '.') > sizeof($aFields), 'UnexpectedValueException',
			'The number of parts in $sName cannot exceeds the size of $aFields.');

		do
		{
			$i								= strrpos($sName, '.', $i);
			$aFilters[array_pop($aFields)]	= substr($sName, $i + 1);
			$sName							= substr($sName, 0, $i);
		}
		while ($i !== null);

		$sWhereClause = '';
		foreach ($aFilters as $sField => $sValue)
			$sWhereClause .= $sField . ' = ' . $this->oDb->escape($sValue) . ' AND ';
		
		return substr($sWhereClause, 0, -5);
	}

	/**
		Returns the name of class to use to build a schema.

		@return string	The schema class name.
		@todo			Maybe we could use get_class($this) . 'Schema' instead?
	*/

	public function getSchemaClass()
	{
		return 'weeDbMetaSchema';
	}

	/**
		Returns the name of class to use to build a table.

		@return string	The table class name.
		@todo			Maybe we could use get_class($this) . 'Table' instead?
	*/

	public function getTableClass()
	{
		return 'weeDbMetaTable';
	}

	/**
		Returns a schema of a given name in the database.

		@param	$sName				The name of the schema.
		@throw	DatabaseException	The schema does not exist in the database.
		@return weeDbMetaSchema		The schema.
	*/

	public function schema($sName)
	{
		$sClass		= $this->getSchemaClass();
		$sQuery		= call_user_func(array($sClass, 'getQuery'));

		return new $sClass($this->oDb, $this->oDb->query(
			$sQuery . ' WHERE schema_name = ?',
			$sName
		)->fetch());
	}

	/**
		Returns all the schemas of the database.

		@return array		The array of schemas.
	*/

	public function schemas()
	{
		$sClass		= $this->getSchemaClass();
		$sQuery		= call_user_func(array($sClass, 'getQuery'));
		$oQuery		= $this->oDb->query($sQuery . ' ORDER BY schema_name');
		$aSchemas	= array();

		foreach ($oQuery as $aSchema)
		{
			$oSchema					= new $sClass($this->oDb, $aSchema);
			$aSchemas[$oSchema->name()] = $oSchema;
		}

		return $aSchemas;
	}

	/**
		Returns a table of a given name in the database.

		@param	$sName				The name of the table.
		@throw	DatabaseException	The table does not exist in the database.
		@return weeDbMetaTable		The table.
	*/

	public function table($sName)
	{
		$sClass		= $this->getTableClass();
		$sQuery		= call_user_func(array($sClass, 'getQuery'));

		return new $sClass($this->oDb, $this->oDb->query(
			$sQuery . '
				WHERE ' . $this->buildWhereClause($sName, array('table_schema, table_name')),
			$sName
		)->fetch());
	}

	/**
		Returns all the tables of a given schema.

		@param	$sSchema	The schema name.
		@return array		The array of tables.
	*/

	public function tables($sSchema)
	{
		$sClass		= $this->getTableClass();
		$sQuery		= call_user_func(array($sClass, 'getQuery'));

		$oQuery = $this->oDb->query(
			$sQuery . '
				WHERE table_schema = ?
				ORDER BY table_name',
			$sSchema);

		$aTables = array();
		foreach ($oQuery as $aTable)
		{
			$oTable						= new $sClass($this->oDb, $aTable);
			$aTables[$oTable->name()]	= $oTable;
		}

		return $aTables;
	}
}
