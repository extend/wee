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

class weeDbMetaSchema implements Printable
{
	/**
		The database to query.
	*/

	protected $oDb;

	/**
		The schema informations.
	*/

	protected $aInfos;

	/**
		Initializes a new schema meta.

		This class should NEVER be instantiated manually.
		An instance of this class should be returned by weeDbMeta
		or by its own static method create.

		@param	$oDb	The database to query.
		@param	$aInfos The schema informations.
		@todo			Some sanity checks on $aInfos?
	*/

	public function __construct(weeDatabase $oDb, array $aInfos)
	{
		fire($oDb == null, 'UnexpectedValueException',
			'$oDb is null.');

		$this->oDb		= $oDb;
		$this->aInfos	= $aInfos;
	}

	/**
		Creates a new instance of the class from a database and a schema name.

		@param	weeDatabase The database.
		@param	string		The schema name.
		@return self		The schema meta.
		@todo				Is this static method really *that* useful?
	*/

	public static function create(weeDatabase $oDb, $sName)
	{
		return $oDb->meta()->schema($sName);
	}

	/**
		Returns the SQL query used to fetch meta infos about schemas.

		@return string	The SQL query.
	*/

	public static function getQuery()
	{
		return 'SELECT schema_name FROM information_schema.schemata';
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
		return $this->oDb->meta()->table($this->name() . '.' . $sName);
	}

	/**
		Returns all the tables of the schema.

		@return array	The array of tables.
	*/

	public function tables()
	{
		return $this->oDb->meta()->tables($this->name());
	}

	/**
		Returns the string representation of the schema.

		@return string	The name of the schema.
	*/

	public function toString()
	{
		return $this->name();
	}
}
