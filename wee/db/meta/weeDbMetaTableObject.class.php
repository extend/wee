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
	Base class for table objects like columns, table constraints and indexes
*/

abstract class weeDbMetaTableObject extends weeDbMetaObject
{
	/**
		The table of the object.
	*/

	protected $oTable;

	/**
		Initializes a new table object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeDbMetaTable.

		@param	$oMeta			The dbmeta object.
		@param	$aData			The table object data.
		@param	weeDbMetaTable	The table of the object.
	*/

	public function __construct(weeDbMeta $oMeta, array $aData, weeDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData);
		$this->oTable = $oTable;
	}

	/**
		Returns the table of the object.

		@return weeDbMetaTable	The table of the object.
	*/

	public function table()
	{
		return $this->oTable;
	}

	/**
		Returns the name of the table of the object.

		@return	string			The name of the table of the object.
	*/

	public function tableName()
	{
		return $this->table()->name();
	}
}
