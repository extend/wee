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
	Class used to query meta informations about tables and their objects.
*/

class weeDbMetaTable
{
	/**
		The database to query.
	*/

	protected $oDb;

	/**
		The table informations.
	*/

	protected $aInfos;

	/**
		Initializes a new table schema.

		This class should NEVER be instantiated manually.
		An instance of this class should be returned by weeDbMeta
		or by its own static method create.

		@param	$oDb	The database to query.
		@param	$aInfos The table informations.
		@todo			Some sanity checks on $aInfos?
	*/

	public function __construct(weeDatabase $oDb, array $aInfos)
	{
		fire($oDb == null, 'UnexpectedValueException',
			'$oDb is null.');

		$this->odB		= $oDb;
		$this->aInfos	= $aInfos;
	}

	/**
		Creates a new instance of the class from a database and a table name.

		@param	weeDatabase The database.
		@param	string		The table name.
		@return self		The table meta.
		@todo				Is this static method really *that* useful?
	*/

	public static function create(weeDatabase $oDb, $sName)
	{
		return $oDb->meta()->table($sName);
	}

	/**
		Returns the SQL query used to fetch meta infos about tables.

		@return string	The SQL query.
	*/

	public static function getQuery()
	{
		return 'SELECT table_schema, table_name, table_type FROM information_schema.tables';
	}

	/**
		Returns the name of the table.
		
		@return string	The name of the table.
	*/

	public function name()
	{
		return $this->aInfos['table_name'];
	}

	/**
		Returns the schema of the table.

		@return weeDbMetaSchema The schema.
	*/

	public function schema()
	{
		return $this->oDb->meta()->schema($this->aInfos['table_schema']);
	}

	/**
		Returns the type of the table.

		@return string	The type of the table.
		@todo			Return a consistent type across the various database drivers.
	*/

	public function type()
	{
		return $this->aInfos['table_type'];
	}

	/**
		Returns the string representation of the table.

		@return string	The fully-qualified table name.
	*/

	public function toString()
	{
		return $this->aInfos['table_schema'] . '.' .$this->name();
	}
}
