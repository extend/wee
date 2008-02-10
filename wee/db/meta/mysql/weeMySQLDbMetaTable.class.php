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

		// These are integers...
		$this->aInfos['version']		= (int) $this->aInfos['version'];
		$this->aInfos['table_rows']		= (int) $this->aInfos['table_rows'];

		// ...but auto_increment can be null.
		if ($this->aInfos['auto_increment'] !== null)
			$this->aInfos['auto_increment']	= (int) $this->aInfos['auto_increment'];
	}

	/**
		Returns the character set of the table.

		@return	string	The character set.
	*/

	public function charset()
	{
		return substr($this->aInfos['table_collation'], 0,
			strpos($this->aInfos['table_collation'], '_'));
	}

	/**
		Returns the comment of the table.

		@return	string	The comment of the table.
	*/

	public function comment()
	{
		return $this->aInfos['table_comment'];
	}

	/**
		Returns the number of rows in the table.

		@see	http://www.php.net/~helly/php/ext/spl/interfaceCountable.html
	*/

	public function count()
	{
		// table_rows is the number of rows in the table, but it is only a rough
		// approximation if the storage engine is InnoDB.

		if ($this->aInfos['engine'] == 'InnoDB')
			return parent::count();

		return $aCount['table_rows'];
	}

	/**
		Returns the array of custom offsets reachable through ArrayAccess interface.
		This class defines two new offsets:
			- charset:	The character set of the table.
			- comment:	The comment of the table.

		@return	array	The array of custom offsets.
	*/

	protected static function getCustomOffsets()
	{
		return array_merge(parent::getCustomOffsets(),
			array('charset', 'comment'));
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
			case 'charset': return $this->charset();
			case 'comment': return $this->comment();
		}

		return parent::getCustomOffset($sOffset);
	}

	/**
		Returns the array of fields which need to be passed to the constructor of the class.

		@return	array	The array of fields.
	*/

	public static function getFields()
	{
		return array_merge(parent::getFields(), array(
			'engine',
			'version',
			'table_rows',
			'auto_increment',
			'table_collation',
			'table_comment'));
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
