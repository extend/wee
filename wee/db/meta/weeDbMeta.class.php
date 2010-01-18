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
	Class used to query meta data about databases and their objects.
*/

abstract class weeDbMeta
	implements weeDbMetaTableProvider
{
	/**
		The database to query.
	*/

	protected $oDb;

	/**
		The DBMS handled by this class.

		Can be either a string or an array of strings.
	*/

	protected $mDBMS;

	/**
		Initializes a new database meta.

		@param	$oDb	The database to query.
		@throw	InvalidArgumentException	The underlying DBMS of the given database is not handled by the class.
	*/

	public function __construct(weeDatabase $oDb)
	{
		$this->mDBMS !== null or burn('IllegalStateException',
			sprintf(_WT('Property $%s is missing.'), 'mDBMS'));
		(is_string($this->mDBMS) ? $oDb->is($this->mDBMS) : in_array($oDb->is(), $this->mDBMS)) or burn('InvalidArgumentException',
			_WT('The underlying DBMS of the given database is not handled by this class.'));
		$this->oDb = $oDb;
	}

	/**
		Returns the associated database object.

		@return	weeDatabase	The associated database object.
	*/

	public function db()
	{
		return $this->oDb;
	}

	/**
		Returns the name of the table class.

		@return	string		The name of the table class.
	*/

	abstract public function getTableClass();

	/**
		Returns all the tables of the database.

		@return	array(weeDbMetaTable)	The array of tables.
	*/

	public function tables()
	{
		$aTables	= array();
		$sClass		= $this->getTableClass();
		foreach ($this->queryTables() as $aTable)
			$aTables[] = new $sClass($this, $aTable);
		return $aTables;
	}

	/**
		Returns the names of all the tables in the database.

		@return	array(string)	The names of all the tables.
	*/

	public function tablesNames()
	{
		$aTables	= array();
		$sClass		= $this->getTableClass();
		foreach ($this->queryTables() as $aTable)
			$aTables[] = $aTable['name'];
		return $aTables;
	}

	/**
		Queries all the tables of the database.

		@return	weeDatabaseResult	The data of all the tables of the database.
	*/

	abstract protected function queryTables();
}
