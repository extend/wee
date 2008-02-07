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
		Builds an array of filters given a dbmeta object name and an array of fields.

		For example, given those parameters:
			$sName = 'bbs.forum';
			$aFields = array('table_space', 'table_name');

		The method will return the following filters:
			array('table_space' => 'bbs', 'table_name' => 'forum');

		If the number of parts in the name is smaller than the number of fields,
		priority is given to the LAST fields.
		
		With the same fields array, given this name:
			$sName = 'forum';

		The method will return the following WHERE clause:
			array('table_name' => 'forum');

		@param	$sName		The object name.
		@param	$aFields	The array of fields.
		@return array		The array of filters.
	*/

	protected static function buildFilters($sName, $aFields)
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
			$aFilters[array_pop($aFields)]			= $sName;

		return $aFilters;
	}


	/**
		Builds a query for a given dbmeta object type.

		@param	$sType	The type of the wanted dbmeta objects.
		@return	string	The SELECT SQL query.
	*/

	protected function buildQuery($sType, $aFilters = array())
	{
		$sClass			= $this->getClass($sType);
		$sTable			= call_user_func(array($sClass, 'getTable'));
		$aFields		= call_user_func(array($sClass, 'getFields'));
		$aOrderFields	= call_user_func(array($sClass, 'getOrderFields'));

		return 'SELECT ' . implode(', ', $aFields) . '
			FROM ' . $sTable . 
			$this->buildWhereClause($aFilters) . '
			ORDER BY ' . implode(', ', $aOrderFields);
	}

	/**
		Builds a SQL WHERE clause from an array of filters.

		For example, given the following array of filters:
			$aFilters = array('table_space' => 'bbs', 'table_name' => 'forum');

		The method will return the following WHERE clause:
			WHERE table_space = 'bbs' AND table_name = 'forum'

		The returned string will be preceded and terminated with a space to be easily
		concatenated in a full SQL request.

		If the provided array is empty, the method will return an empty string.

		@param	$aFilters	The array of filters.
		@return string		The SQL WHERE clause.
	*/

	protected function buildWhereClause(array $aFilters)
	{
		if (empty($aFilters))
			return '';

		$sWhere = ' WHERE ';
		foreach ($aFilters as $sField => $mValue)
			$sWhere .= $sField . '=' . $this->oDb->escape($mValue) . ' AND ';

		return substr($sWhere, 0, -4);
	}

	/**
		Returns a column of a given name in the database.

		@param	$sName			The name of the column.
		@return	weeDbMetaColumn	The column.
	*/

	public function column($sName)
	{
		$aColumns = $this->create('column', self::buildFilters($sName,
			array('table_schema', 'table_name', 'column_name')));

		fire(count($aColumns) == 0, 'UnexpectedValueException',
			"The name '$sName' did not match any column in the database.");

		fire(count($aColumns) != 1, 'UnexpectedValueException',
			"The name '$sName' matched multiple columns in the database.");

		return array_shift($aColumns);
	}

	/**
		Returns all the columns of a given table in the database.

		@return array	The array of schemas.
		@bug			A table name which is not fully-qualified may match multiple
						tables in the database, we should prevent that.
	*/

	public function columns($sTable)
	{
		return $this->create('column', self::buildFilters($sTable,
			array('table_schema', 'table_name')));
	}

	/**
		Creates an array of dbmeta objects of a given type which matches an array of filters.
		The keys of the returned array are the names of the matched dbmeta objects.

		@param	$sType		The type of the dbmeta objects.
		@param	$aFilters	The array of filters.
		@param	array		The requested dbmeta objects.
	*/

	protected function create($sType, array $aFilters = array())
	{
		$sClass		= $this->getClass($sType);
		$oResults	= $this->oDb->query($this->buildQuery($sType, $aFilters));
		$aObjects	= array();

		foreach ($oResults as $aRow)
		{
			$oObject					= new $sClass($this->oDb, $aRow);
			$aObjects[$oObject->name()]	= $oObject;
		}

		return $aObjects;
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
			"'$sType' dbmeta object type is not associated with any class.");
	}

	/**
		Returns a schema of a given name in the database.

		@param	$sName				The name of the schema.
		@return weeDbMetaSchema		The schema.
	*/

	public function schema($sName)
	{
		$aSchemas = $this->create('schema', self::buildFilters($sName,
			array('schema_name')));

		fire(count($aSchemas) == 0, 'UnexpectedValueException',
			"The name '$sName' did not match any schema in the database.");

		return array_shift($aSchemas);
	}

	/**
		Returns all the schemas of the database.

		@return array		The array of schemas.
	*/

	public function schemas()
	{
		return $this->create('schema');
	}

	/**
		Returns a table of a given name in the database.

		@param	$sName			The name of the table.
		@return weeDbMetaTable	The table.
	*/

	public function table($sName)
	{
		$aTables = $this->create('table', self::buildFilters($sName,
			array('table_schema', 'table_name')));

		fire(count($aTables) == 0, 'UnexpectedValueException',
			"The name '$sName' did not match any table in the database.");

		fire(count($aTables) != 1, 'UnexpectedValueException',
			"The name '$sName' matched multiple tables in the database.");

		return array_shift($aTables);
	}

	/**
		Returns all the tables of a given schema.

		@param	$sSchema	The schema name.
		@return array		The array of tables.
	*/

	public function tables($sSchema)
	{
		return $this->create('table', self::buildFilters($sSchema,
			array('table_schema')));
	}
}
