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
	SQLite specialisation of weeDbMetaColumn.
*/

class weeSQLiteDbMetaColumn extends weeDbMetaColumn
{
	/**
		Initialises a new sqlite column object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeSQLiteDbMetaTable.

		@param	$oMeta	The dbmeta object.
		@param	$aData	The object data.
		@param	$oTable	The table of the column.
	*/

	public function __construct(weeSQLiteDbMeta $oMeta, array $aData, weeSQLiteDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData, $oTable);
	}

	/**
		Returns the default value of the column.

		@return	string					The default value of the column.
		@throw	IllegalStateException	The column does not have a default value.
	*/

	public function defaultValue()
	{
		$this->hasDefault()
			or burn('IllegalStateException',
				_WT('The column does not have a default value.'));
		return $this->aData['default'];
	}

	/**
		Does the sqlite-dependent logic of getValidator.

		The only handled type is INTEGER PRIMARY KEY.

		@return	weeValidator	A validator appropriate for the column or null.
		@see					http://sqlite.org/datatypes.html
	*/

	protected function doGetValidator()
	{
		if ($this->aData['type'] == 'INTEGER' && $this->oTable->hasPrimaryKey()
				&& $this->oTable->primaryKeyColumnsNames() == array($this->aData['name']))
			return new weeNumberValidator;
	}

	/**
		Returns whether the column has a default value.

		@return	bool	Whether the column has a default value.
	*/

	public function hasDefault()
	{
		return $this->isNullable() || $this->aData['default'] !== null;
	}

	/**
		Returns whether the column can contain null values.
	
		@return	bool	Whether the column can contain null values.
	*/

	public function isNullable()
	{
		return $this->aData['nullable'];
	}
}
