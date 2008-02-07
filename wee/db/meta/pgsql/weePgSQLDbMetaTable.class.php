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
	PostgreSQL specialization of weeDbMetaTable.
*/

class weePgSQLDbMetaTable extends weeDbMetaTable
{
	/**
		Temporary table type
	*/

	const TEMPORARY = 3;

	/**
		Initializes a new pgsql metadb table object.

		@see	weeDbMetaTable::__construct()
	*/

	public function __construct(weePgSQLDbMeta $oMeta, array $aInfos)
	{
		parent::__construct($oMeta, $aInfos);

		// is_insertable_into field contains either 'YES' or 'NO', we convert
		// it to a boolean value.
		$this->aInfos['is_insertable_into'] = $this->aInfos['is_insertable_into'] == 'YES';
	}

	/**
		Returns the array of fields which need to be passed to the constructor of the class.

		@return	array	The array of fields.
	*/

	public static function getFields()
	{
		return array_merge(parent::getFields(), array(
			'is_insertable_into'));
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
			case 'LOCAL TEMPORARY': return self::TEMPORARY;
		}

		return parent::type();
	}
}
