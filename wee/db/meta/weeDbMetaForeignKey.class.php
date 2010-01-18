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
	Class used to query meta data about foreign keys.
*/

abstract class weeDbMetaForeignKey extends weeDbMetaTableObject
{
	/**
		Returns the columns of the foreign key.

		@return	array(string)	The names of the columns of the foreign key.
	*/

	abstract public function columnsNames();

	/**
		Returns the referenced columns of the foreign key.

		@return	array(string)	The names of the referenced columns of the foreign key.
	*/

	abstract public function referencedColumnsNames();

	/**
		Returns the name of the referenced table of the foreign key.

		@return	string			The name of the referenced table of the foreign key.
	*/

	public function referencedTableName()
	{
		return $this->aData['referenced_table'];
	}
}
