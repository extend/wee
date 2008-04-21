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

class weeDbMetaTable extends weeDbMetaObject implements Countable
{
	/**
		Base table type
	*/

	const BASE_TABLE = 1;

	/**
		View type
	*/

	const VIEW = 2;

	/**
		Returns a column of the table.

		@param	$sName				The column name.
		@throw	DatabaseException	The column does not exist in the table.
		@return	weeDbMetaColumn		The column.
	*/

	public function column($sName)
	{
		return $this->oMeta->column($this->toString() . '.' . $sName);
	}

	/**
		Returns all the columns of the table.

		@param	array				The array of tables.
	*/

	public function columns()
	{
		return $this->oMeta->columns($this->toString());
	}

	/**
		Returns the number of rows in the table.

		@see	http://www.php.net/~helly/php/ext/spl/interfaceCountable.html
	*/

	public function count()
	{
		$aCount = $this->oMeta->db()->query(
			'SELECT COUNT(*) AS table_rows FROM ' . $this->toString());

		return (int) $aCount['table_rows'];
	}

	/**
		Returns the array of fields which need to be passed to the constructor of the class.

		@return	array	The array of fields.
	*/

	public static function getFields()
	{
		return array('table_schema', 'table_name', 'table_type');
	}

	/**
		Returns the array of fields used to order the objects in the SQL SELECT query.

		@return	array	The array of order fields.
	*/

	public static function getOrderFields()
	{
		return array('table_schema', 'table_name');
	}

	/**
		Returns an array containing the column names of the primary key of the table.
		@return	array	The array of column names
	*/

	public function primaryKey()
	{
		$a = array();
		$oQuery = $this->oMeta->db()->query(
			'SELECT kcu.column_name
				FROM	information_schema.key_column_usage		kcu,
						information_schema.table_constraints	tc
				WHERE		tc.table_schema			= ?
						AND tc.table_name			= ?
						AND tc.constraint_type		= ?
						AND kcu.constraint_schema	= tc.constraint_schema
						AND kcu.table_schema		= tc.table_schema
						AND kcu.table_name			= tc.table_name
						AND kcu.constraint_name		= tc.constraint_name
				ORDER BY kcu.ordinal_position',
			$this->aInfos['table_schema'],
			$this->aInfos['table_name'],
			'PRIMARY KEY');

		foreach ($oQuery as $aColumn)
			$a[] = $aColumn['column_name'];

		return $a;
	}

	/**
		Returns the name of the information_schema table where the dbmeta objects
		are stored.

		@return	string	The table name.
	*/

	public static function getTable()
	{
		return 'information_schema.tables';
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

	protected function schema()
	{
		return $this->oMeta->schema($this->aInfos['table_schema']);
	}

	/**
		Returns the type of the table.

		The type is either one of the two BASE_TABLE, VIEW
		class constants.

		@return int	The table type.
	*/

	public function type()
	{
		switch ($this->aInfos['table_type'])
		{
			case 'BASE TABLE':	return self::BASE_TABLE;
			case 'VIEW':		return self::VIEW;
		}

		burn('IllegalStateException',
			"'" . $aInfos['table_type'] . "' is not a known table type.");
	}
}
