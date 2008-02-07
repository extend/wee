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
	Class used to query meta informations about columns and their objects.
*/

class weeDbMetaColumn extends weeDbMetaObject
{
	/**
		Initializes a new metadb column object.

		We override the default constructor from weeDbMetaObject to do some
		modifications on the informations.

		@see	weeDbMetaObject::__construct()
	*/

	public function __construct(weeDbMeta $oMeta, array $aInfos)
	{
		parent::__construct($oMeta, $aInfos);

		// These fields are integers.
		$this->aInfos['ordinal_position']	= (int) $this->aInfos['ordinal_position'];
		$this->aInfos['numeric_precision']	= (int) $this->aInfos['numeric_precision'];
		$this->aInfos['numeric_scale']		= (int) $this->aInfos['numeric_scale'];

		// is_nullable field contains either 'YES' or 'NO', we convert
		// it to a boolean value.
		$this->aInfos['is_nullable']		= $this->aInfos['is_nullable'] == 'YES';
	}

	/**
		Returns the array of custom offsets reachable through ArrayAccess interface.
		This class defines four new offsets:
			- schema:		Returns a weeDbMetaSchema object for the schema of the column.
			- table:		Returns a weeDbMetaTable object for the table of the column.
			- position:		Returns the position of the column in the table.
			- type:			Returns the type of the column.

		@return	array	The array of custom offsets.
		@todo			Define more custom offsets.
	*/

	protected static function getCustomOffsets()
	{
		return array_merge(parent::getCustomOffsets(),
			array('schema', 'table', 'position', 'type'));
	}

	/**
		Returns the value of a custom offset.

		@param	$sOffset	The custom offset.
		@return mixed		The value associated with the custom offset.
	*/

	protected function getCustomOffset($sOffset)
	{
		switch ($sOffset)
		{
			case 'schema':		return $this->schema();
			case 'table':		return $this->table();
			case 'position':	return $this->aInfos['ordinal_position'];
			case 'type':		return $this->type();
		}

		return parent::getCustomOffset($sOffset);
	}

	/**
		Returns the array of fields which need to be passed to the constructor of the class.

		@return	array	The array of fields.
		@todo			Handle more fields.
	*/

	public static function getFields()
	{
		return array(
			'table_schema',
			'table_name',
			'column_name',
			'ordinal_position',
			'column_default',
			'is_nullable',
			'data_type',
			'character_maximum_length',
			'numeric_precision',
			'numeric_scale');
	}

	/**
		Returns the array of fields used to order the objects in the SQL SELECT query.

		@return	array	The array of order fields.
	*/

	public static function getOrderFields()
	{
		return array(
			'table_schema',
			'table_name',
			'ordinal_position');
	}

	/**
		Returns the name of the information_schema table where the column objects
		are stored.

		@return	string	The table name.
	*/

	public static function getTable()
	{
		return 'information_schema.columns';
	}

	/**
		Returns the name of the column.
		
		@return string	The name of the column.
	*/

	public function name()
	{
		return $this->aInfos['column_name'];
	}

	/**
		Returns the schema of the column.

		@return weeDbMetaSchema The schema.
	*/

	protected function schema()
	{
		return $this->oMeta->schema($this->aInfos['table_schema']);
	}

	/**
		Returns the table of the column.

		@return weeDbMetaTable	The table.
	*/

	protected function table()
	{
		return $this->oMeta->table(
			$this->aInfos['table_schema'] . '.' . $this->aInfos['table_name']);
	}

	/**
		Returns the string representation of the column.

		@return	string	The fully-qualified column name.
	*/

	public function toString()
	{
		return $this->aInfos['table_schema']
			. '.' . $this->aInfos['table_name']
			. '.' . $this->name();
	}
}
