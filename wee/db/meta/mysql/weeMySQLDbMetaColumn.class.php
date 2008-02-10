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
	MySQL specialization of weeDbMetaColumn.
*/

class weeMySQLDbMetaColumn extends weeDbMetaColumn
{
	/**
		Primary key.
	*/

	const PRIMARY_KEY = 1;

	/**
		Unique key.
	*/

	const UNIQUE_KEY = 2;

	/**
		Foreign key.
	*/

	const FOREIGN_KEY = 3;

	/**
		Initializes a new mysql metadb column object.

		@see	weeDbMetaColumn::__construct()
	*/

	public function __construct(weeMySQLDbMeta $oMeta, array $aInfos)
	{
		parent::__construct($oMeta, $aInfos);
	}

	/**
		Returns the comment of the column.

		@return	string	The comment.
	*/

	public function comment()
	{
		return $this->aInfos['column_comment'];
	}

	/**
		Returns the array of fields which need to be passed to the constructor of the class.

		@return	array	The array of fields.
		@todo			Handle more fields.
	*/

	public static function getFields()
	{
		return array_merge(parent::getFields(), array(
			'character_set_name',
			'collation_name',
			'column_key',
			'extra',
			'column_comment'));
	}

	/**
		Returns the array of custom offsets reachable through ArrayAccess interface.
		This class defines four new offsets:
			- comment:				The comment of the column.
			- is_auto_incremented:	Whether the column is auto incremented.
			- key_type:				The type of the key in which the column takes part.

		@return	array	The array of custom offsets.
		@todo			Define more custom offsets.
	*/

	protected static function getCustomOffsets()
	{
		return array_merge(parent::getCustomOffsets(),
			array('comment', 'is_auto_incremented', 'key'));
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
			case 'comment':				return $this->comment();
			case 'key_type':			return $this->keyType();
			case 'is_auto_incremented':	return $this->isAutoIncremented();
		}

		return parent::getCustomOffset($sOffset);
	}

	/**
		Returns whether the column is auto incremented or not.

		@return	boolean	True is the column is auto incremented, false otherwise.
	*/

	public function isAutoIncremented()
	{
		return $this->aInfos['extra'] == 'AUTO_INCREMENT';
	}

	/**
		Return the type of the key in which the key takes part, or null if any.

		The type is one of the three PRIMARY_KEY, UNIQUE_KEY, FOREIGN_KEY class constants.

		@return	int	The type of the key or null.
		@todo		Check whether foreign keys are really indicated with FOR.
	*/

	public function keyType()
	{
		switch ($aInfos['column_key'])
		{
			case 'PRI':	return self::PRIMARY_KEY;
			case 'UNI':	return self::UNIQUE_KEY;
			case 'FOR':	return self::FOREIGN_KEY;
			case '':	return null;
		}

		burn('UnexpectedValueException',
			"'" . $Infos['column_key'] . '" is not a valid key type.');
	}
}
