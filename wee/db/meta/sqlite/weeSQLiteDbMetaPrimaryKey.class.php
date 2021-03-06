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
	SQLite specialisation of weeDbMetaPrimaryKey.

	In SQLite, primary keys do not have a name so the name method
	always returns null.
*/

class weeSQLiteDbMetaPrimaryKey extends weeDbMetaPrimaryKey
{
	/**
		Initialises a new sqlite primary key object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeSQLiteDbMetaTable.

		@param	$oMeta	The dbmeta object.
		@param	$aData	The object data.
		@param	$oTable	The table of the primary key.
	*/

	public function __construct(weeSQLiteDbMeta $oMeta, array $aData, weeSQLiteDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData, $oTable);
	}

	/**
		Returns the names of the columns of the primary key.

		@return	array(string)	The names of the columns of the primary key.
	*/

	public function columnsNames()
	{
		return $this->aData['columns'];
	}

	/**
		Returns the name of the primary key.

		@return	null	Primary keys in SQLite do not have a name.
	*/

	public function name()
	{
		return null;
	}
}
