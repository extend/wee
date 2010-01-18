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
	MSSQL specialisation of weeDbMetaColumn.
*/

class weeMSSQLDbMetaColumn extends weeDbMetaColumn 
	implements weeDbMetaCommentable, weeDbMetaSchemaObject
{
	/**
		Initialise a new mssql column object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeMSSQLDbMetaTable.

		@param	$oMeta	The mssql dbmeta object.
		@param	$aData	The column data.
		@param	$oTable	The mssql table of the column.
	*/

	public function __construct(weeMSSQLDbMeta $oMeta, array $aData, weeMSSQLDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData, $oTable);
	}

	/**
		Returns the comment of the column.

		@return string The comment.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Return the default value of the column.

		@return	string	The default value of the column.
		@throw	IllegalStateException	The column does not have a default value.
	*/

	public function defaultValue()
	{
		$this->hasDefault() or burn('IllegalStateException',
			_WT('The column does not have a default value.'));

		// Strip wrapping parentheses.
		$i = strspn($this->aData['default'], '(');
		return substr($this->aData['default'], $i, - $i);
	}

	/**
		Does the oracle-dependent logic of getValidator.

		Handled types:
		 * CHAR
		 * VARCHAR2
		 * NCHAR
		 * NVARCHAR2
		 * LONG
		 * NUMBER
		 * BINARY_FLOAT
		 * BINARY_DOUBLE

		@return	weeValidator	A validator appropriate for the column or null.
		@todo	Handle NUMBER precision and scale properly.
		@todo	Support date types (will need to handle the NLS_DATE_FORMAT parameter)
		@see	http://www.oracle.com/pls/xe102/lookup?id=CNCPT012
	*/

	protected function doGetValidator()
	{
		if ($this->aData['type_schema'] !== null)
			// The type is not a native datatype.
			return;

		switch ($this->aData['type'])
		{
			case 'CHAR':
			case 'VARCHAR2':
			case 'NCHAR':
			case 'NVARCHAR2':
				return new weeStringValidator(array('max' => $this->aData['char_length']));

			case 'LONG':
				return new weeStringValidator;

			case 'NUMBER':
				if ((int)$this->aData['data_scale'] <= 0)
					return new weeBigNumberValidator;

			case 'BINARY_FLOAT':
			case 'BINARY_DOUBLE':
				return new weeBigNumberValidator(array('format' => 'float'));
		}
	}

	/**
		Return whether the column has a default value.

		@return	bool	true if the column has a default value, false otherwise.
	*/

	public function hasDefault()
	{
		return $this->isNullable() || $this->aData['default'] !== null;
	}

	/**
		Returns whether the column can contain null values.
	
		@return	bool	true if the column accepts null as a value, false otherwise.
	*/

	public function isNullable()
	{
		return $this->aData['nullable'] == 'YES';
	}

	/**
		Returns the name of the schema of the column.

		@return	string	The name of the schema.
	*/

	public function schemaName()
	{
		return $this->aData['schema'];
	}
}
