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
	MySQL specialization of weeDbMetaColumn.
*/

class weeMySQLDbMetaColumn extends weeDbMetaColumn implements weeDbMetaCommentable
{
	/**
		Initializes a new mysql column object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeMySQLDbMetaTable.

		@param	$oMeta	The dbmeta object.
		@param	$aData	The object data.
		@param	$oTable	The table of the column.
	*/

	public function __construct(weeMySQLDbMeta $oMeta, array $aData, weeMySQLDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData, $oTable);
	}

	/**
		Returns the comment of the column.

		@return	string	The comment of the column.
	*/

	public function comment()
	{
		return $this->aData['comment'];
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
			_WT('The column does not have any default value.'));

		return $this->aData['default'];
	}

	/**
		Returns a validator for the column.

		Handled types:
		 - tinyint
		 - smallint
		 - int
		 - bigint
		 - char
		 - varchar
		 - date
		 - time (range restricted from 00:00:00 to 23:59:59)

		@return	weeValidator	A validator appropriate for the column.
		@see					http://dev.mysql.com/doc/refman/5.0/en/data-types.html
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
				$aBounds = array(
					'tinyint'		=> array('min' => -128,						'max' => 127),
					'smallint'		=> array('min' => -32768,					'max' => 32767),
					'mediumint'		=> array('min' => -8388608,					'max' => 8388607),
					'int'			=> array('min' => -2147483648,				'max' => 2147483647),
					'bigint'		=> array('min' => '-9223372036854775808',	'max' => '9223372036854775807')
				);

				$aArgs		= $aBounds[$this->aData['type']];
				$bUnsigned	= !substr_compare($this->aData['raw_type'], 'unsigned', -8);

				if ($bUnsigned)
				{
					switch ($this->aData['type'])
					{
						case 'int':
							$aArgs = array('min' => '0', 'max' => '4294967295');
							break;

						case 'bigint':
							$aArgs = array('min' => '0', 'max' => '18446744073709551615');
							break;

						default:
							$aArgs = array('min' => 0, 'max' => $aArgs['max'] - $aArgs['min']);
					}
				}

				if (is_string($aArgs['min']))
					return new weeBigNumberValidator($aArgs);
				else
					return new weeNumberValidator($aArgs);

			case 'char':
			case 'varchar':
				return new weeStringValidator(array('max' => $this->aData['max_length']));

			case 'date':
				return new weeDateValidator(array('min' => '1000-01-01', 'max' => '9999-12-31'));

			case 'time':
				// the real range of the time type is '-838:59:59' to '838:59:59',
				// we restrict here to '00:00:00' to '23:59:59'.
				return new weeTimeValidator;

			default:
				burn('UnhandledTypeException',
					sprintf(_WT('Type "%s" is not handled by dbmeta.'), $this->aData['type']));
		}
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
		return $this->aData['nullable'] == 'YES';
	}
}
