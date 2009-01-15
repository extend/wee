<?php

/*
	Web:Extend
	Copyright (c) 2006-2009 Dev:Extend

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
	Used to represent a foreign key which driver support schema objects.
*/

abstract class weeDbMetaSchemaForeignKey extends weeDbMetaForeignKey
	implements weeDbMetaSchemaObject
{
	/**
		Returns the name of the schema in which is the referenced table.

		@return	string	The name of the referenced schema.
	*/

	public function referencedSchemaName()
	{
		return $this->aData['referenced_schema'];
	}

	/**
		Returns the name of the schema in which is the foreign key.

		@return	string	The name of the schema in which is the foreign key.
	*/

	public function schemaName()
	{
		return $this->aData['schema'];
	}
}
