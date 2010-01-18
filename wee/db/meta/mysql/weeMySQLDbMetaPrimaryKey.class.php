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
	MySQL specialization of weeDbMetaPrimaryKey.

	In MySQL, primary keys cannot have a custom name, they are always named "PRIMARY".
*/

class weeMySQLDbMetaPrimaryKey extends weeDbMetaPrimaryKey
{
	/**
		Initializes a new mysql primary key object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeMySQLDbMetaTable.

		@param	$oMeta			The mysql dbmeta object.
		@param	$aData			The primary key data.
		@param	$oTable			The mysql table of the primary key.
	*/

	public function __construct(weeMySQLDbMeta $oMeta, array $aData, weeMySQLDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData, $oTable);
	}

	/**
		Returns the names of the columns of the primary key.

		The columns are ordered as specified in the definition of the table.

		@return	array(string)	The names of the columns of the primary key.
	*/

	public function columnsNames()
	{
		$oQuery = $this->db()->query("
			SELECT			COLUMN_NAME
				FROM		information_schema.COLUMNS
				WHERE		TABLE_NAME		= ?
						AND	TABLE_SCHEMA	= DATABASE()
						AND COLUMN_KEY		= 'PRI'
				ORDER BY	ORDINAL_POSITION
		", $this->table()->name());

		$aColumns = array();
		foreach ($oQuery as $aColumn)
			$aColumns[] = $aColumn['COLUMN_NAME'];
		return $aColumns;
	}
}
