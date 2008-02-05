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
		Build a query for a given dbmeta class and information_schema table.

		This method gets an array of SQL fields from the given class and returns
		the corresponding SELECT SQL query.

		The given dbmeta class must extends weeDbMetaObject.

		@param	$sClass						The dbmeta class.
		@param	$sTable						The information_schema table.
		@return	string						The SELECT SQL query.
		@throw	UnexpectedValueException	The class does not extends weeDbMetaObject.
	*/

	protected static function buildQuery($sClass, $sTable)
	{
		fire(!is_subclass_of($sClass, 'weeDbMetaObject'), 'UnexpectedValueException',
			"'$sClass' does not extends weeDbMetaObject");

		$aFields = call_user_func(array($sClass, 'getFields'));
		return 'SELECT ' . implode(', ', $aFields) . ' FROM information_schema.' . $sTable;
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

	protected function buildWhereClause($sName, $aFields)
	{
		fire(substr_count($sName, '.') > sizeof($aFields), 'UnexpectedValueException',
			'The number of parts in $sName cannot exceeds the size of $aFields.');

		$aFilters = array();
		for ($i = strrpos($sName, '.'); $i !== false; $i = strrpos($sName, '.', $i))
		{
			$aFilters[array_pop($aFields)]	= substr($sName, $i + 1);
			$sName							= substr($sName, 0, $i);
		}

		if (!empty($aFields))
			$aFilters[array_pop($aFields)]	= $sName;

		$sWhereClause = '';
		foreach ($aFilters as $sField => $sValue)
			$sWhereClause .= $sField . ' = ' . $this->oDb->escape($sValue) . ' AND ';
		return substr($sWhereClause, 0, -5);
	}

	/**
		Returns a column of a given name in the database.

		@param	$sName						The name of the column.
		@throw	DatabaseException			The column does not exist in the database.
		@throw	UnexpectedValueException	The name matched multiple columns in the database.
		@return	weeDbMetaColumn				The column.
	*/

	public function column($sName)
	{
		$sClass = $this->getClass('column');
		$sQuery	= self::buildQuery($sClass, 'columns');

		$oQuery = $this->oDb->query(
			$sQuery . ' WHERE ' . $this->buildWhereClause($sName, array(
				'table_schema', 'table_name', 'column_name')));

		fire(count($oQuery) > 1, 'UnexpectedValueException',
			"The name '$sName' matched multiple columns in the database.");

		return new $sClass($this->oDb, $oQuery->fetch());
	}

	/**
		Returns all the columns of a given table in the database.

		@return array		The array of schemas.
		@bug				A table name which is not fully-qualified may match multiple
							tables in the database, we should prevent that.
	*/

	public function columns($sTable)
	{
		$sClass		= $this->getClass('column');
		$sQuery		= self::buildQuery($sClass, 'columns');
		$aColumns	= array();

		// TODO remove table_name one from the ORDER clause when the bug is taken care of.
		$oQuery = $this->oDb->query(
			$sQuery . ' WHERE ' . $this->buildWhereClause($sTable, array(
				'table_schema', 'table_name')) . '
				ORDER BY table_name, column_name');

		foreach ($oQuery as $aColumn)
		{
			$oColumn					= new $sClass($this->oDb, $aColumn);
			$aColumns[$oColumn->name()]	= $oColumn;
		}

		return $aColumns;
	}

	/**
		Returns the name of class used to build a dbmeta object of a given type.

		@param	$sType	The type of the dbmeta object.
	*/

	public function getClass($sType)
	{
		$sSuffix	= ucfirst($sType);
		$sMetaClass	= get_class($this);
		$sClass		= $sMetaClass . $sSuffix;
		if (class_exists($sClass))
			return $sClass;

		while (($sMetaClass = get_parent_class($sMetaClass)) !== false)
		{
			$sClass = $sMetaClass . $sSuffix;
			if (class_exists($sClass))
				return $sClass;
		}

		burn('UnexpectedValueException',
			"'$sType' dbmeta object type is not associated with any class");
	}

	/**
		Returns a schema of a given name in the database.

		@param	$sName				The name of the schema.
		@throw	DatabaseException	The schema does not exist in the database.
		@return weeDbMetaSchema		The schema.
	*/

	public function schema($sName)
	{
		$sClass = $this->getClass('schema');
		$sQuery	= $this->buildQuery($sClass, 'schemata');

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
		$sClass		= $this->getClass('schema');
		$sQuery		= self::buildQuery($sClass, 'schemata');
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

		@param	$sName						The name of the table.
		@throw	DatabaseException			The table does not exist in the database.
		@throw	UnexpectedValueException	The name matched multiple tables in the database.
		@return weeDbMetaTable				The table.
	*/

	public function table($sName)
	{
		$sClass = $this->getClass('table');
		$sQuery	= self::buildQuery($sClass, 'tables');

		$oQuery = $this->oDb->query($sQuery . '
			WHERE ' . $this->buildWhereClause($sName, array('table_schema', 'table_name')));
		
		fire(count($oQuery) > 1, 'UnexpectedValueException',
			"The name '$sName' matched multiple tables in the database.");

		return new $sClass($this->oDb, $oQuery->fetch());
	}

	/**
		Returns all the tables of a given schema.

		@param	$sSchema	The schema name.
		@return array		The array of tables.
	*/

	public function tables($sSchema)
	{
		$sClass		= $this->getClass('table');
		$sQuery		= $this->buildQuery($sClass, 'tables');

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
