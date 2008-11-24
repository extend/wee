<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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
	Class used to query meta data about columns and their objects.
*/

abstract class weeDbMetaColumn extends weeDbMetaTableObject
{
	/**
		Returns the default value of the column.

		@return	string	The default value of the column.
	*/

	abstract public function defaultValue();

	/**
		Returns a validator for the column.

		@return	weeValidator			A validator appropriate for the column.
		@throw	UnhandledTypeException	The type of the column is not handled by dbmeta.
	*/

	public function getValidator()
	{
		switch ($this->aData['type'])
		{
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'int':
			case 'bigint':
				return weeDbMeta::getIntegerValidator($this->aData['type']);

			case 'char':
			case 'varchar':
				return new weeStringValidator(array('max' => $this->aData['max_length']));

			default:
				burn('UnhandledTypeException',
					sprintf(_WT('Type "%s" is not handled by dbmeta.'), $this->aData['type']));
		}
	}

	/**
		Returns whether the column has a default value.

		@return	bool	Whether the column has a default value.
	*/

	abstract public function hasDefault();

	/**
		Returns whether the column can contain null values.
	
		@return	bool	Whether the column can contain null values.
	*/

	abstract public function isNullable();

	/**
		Returns the number of the column in the table.

		@return	int		The number of the column in the table.
	*/

	public function num()
	{
		return (int)$this->aData['num'];
	}
}
