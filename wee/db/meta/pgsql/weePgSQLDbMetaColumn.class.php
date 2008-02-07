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
	PostgreSQL specialization of weeDbMetaColumn.
*/

class weePgSQLDbMetaColumn extends weeDbMetaColumn
{
	/**
		Initializes a new pgsql metadb column object.

		@see	weeDbMetaColumn::__construct()
	*/

	public function __construct(weeDbMeta $oMeta, array $aInfos)
	{
		parent::__construct($oMeta, $aInfos);

		// is_updatable field contains either 'YES' or 'NO', we convert
		// it to a boolean value.
		$this->aInfos['is_updatable'] = $this->aInfos['is_updatable'] == 'YES';
	}

	/**
		Returns the array of fields which need to be passed to the constructor of the class.

		@return	array	The array of fields.
		@todo			Handle more fields.
	*/

	public static function getFields()
	{
		return array_merge(parent::getFields(), array(
			'is_updatable'));
	}
}
