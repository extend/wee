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
	MySQL specialization of weeDbMetaTable.
*/

class weeMySQLDbMetaTable extends weeDbMetaTable
{
	/**
		Temporary table type
	*/

	const TEMPORARY = 3;

	/**
		Initializes a new mysql metadb table object.

		@see	weeDbMetaTable::__construct()
	*/

	public function __construct(weeMySQLDbMeta $oMeta, array $aInfos)
	{
		parent::__construct($oMeta, $aInfos);
	}

	/**
		Returns the array of fields which need to be passed to the constructor of the class.

		@return	array	The array of fields.
	*/

	public static function getFields()
	{
		return array_merge(parent::getFields(), array(
			));
	}

	/**
		Returns the type of the table.

		The type is either one of the three BASE_TABLE, VIEW, TEMPORARY
		class constants.

		@return int	The table type.
	*/

	public function type()
	{
		switch ($this->aInfos['table_type'])
		{
			case 'TEMPORARY': return self::TEMPORARY;
		}

		return parent::type();
	}
}
