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
	MySQL specialization of weeDbMetaPrimaryKey.

	In MySQL, primary keys cannot have a custom name, they are always named "PRIMARY".
*/

class weeMySQLDbMetaPrimaryKey extends weeDbMetaPrimaryKey
{
	/**
		Returns the columns of the table constraint.

		The columns are ordered as specified in the definition of the table.

		@return	array(string)	The names of the columns of the constraint.
	*/

	public function columns()
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
