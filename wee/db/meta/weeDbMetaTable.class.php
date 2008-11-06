<?php

/*
	Web:Extend
	Copyright (c) 2008 Dev:Extend

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
	Class used to query meta data about tables and their objects.
*/

abstract class weeDbMetaTable extends weeDbMetaObject
{
	/**
		Returns the name of the column class.

		@return	string					The name of the column class.
	*/

	abstract public function getColumnClass();

	/**
		Returns the name of the primary key class.

		@return	string					The name of the primary key class.
	*/

	abstract public function getPrimaryKeyClass();

	/**
		Returns a column of the table.

		@param	$sName					The column name.
		@return	weeDbMetaColumn			The column.
	*/

	abstract public function column($sName);

	/**
		Returns whether a given column exists in the table.

		@param	$sName					The column name.
		@return	bool					true if the column exists, false otherwise.
	*/

	abstract public function columnExists($sName);

	/**
		Returns all the columns of the table.

		@return	array(weeDbMetaColumn)	The array of tables.
	*/

	abstract public function columns();

	/**
		Returns whether the table has a primary key.

		@return	bool					true if the table has a primary key, false otherwise.
	*/

	abstract public function hasPrimaryKey();

	/**
		Instantiates a new table object.

		@param	$sClass						The class of the table object.
		@param	$aData						The table object data.
		@return	weeDbMetaTableObject		The table object.
		@throw	InvalidArgumentException	The class is not a subclass of weeDbMetaTableObject.
	*/

	protected function instantiateObject($sClass, array $aData)
	{
		@is_subclass_of($sClass, 'weeDbMetaTableObject')
			or burn('InvalidArgumentException',
				_WT('The class is not a subclass of weeDbMetaTableObject.'));

		$oObject = new $sClass($this->meta(), $aData, $this);
		return $oObject;
	}

	/**
		Returns the primary key of the table.

		@return	weeDbMetaPrimaryKey			The primary key of the table.
	*/

	abstract public function primaryKey();
}
