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
	Class used to query meta data about columns and their objects.
*/

abstract class weeDbMetaColumn extends weeDbMetaTableObject
{
	/**
		The validator of the column.

		Lazily set by hasValidator.
	*/

	protected $oValidator = false;

	/**
		Returns the default value of the column.

		@return	string	The default value of the column.
	*/

	abstract public function defaultValue();

	/**
		Does the database-dependent logic of getValidator.

		This method should return null if the column type is not handled by DbMeta.

		@return	mixed	A validator appropriate for the column or null.
	*/

	abstract protected function doGetValidator();

	/**
		Returns a validator for the column.

		@return	weeValidator			A validator appropriate for the column.
		@throw	UnhandledTypeException	The type of the column is not handled by DbMeta.
	*/

	public function getValidator()
	{
		$this->hasValidator()
			or burn('UnhandledTypeException',
				_WT('The type of the column is not handled by DbMeta.'));
		return $this->oValidator;
	}

	/**
		Returns whether the column has a default value.

		@return	bool	Whether the column has a default value.
	*/

	abstract public function hasDefault();

	/**
		Returns whether the column has a validator.

		@return bool	Whether the column has a validator.
	*/

	public function hasValidator()
	{
		if ($this->oValidator === false)
			$this->oValidator = $this->doGetValidator();
		return $this->oValidator !== null;
	}

	/**
		Returns whether the column can contain null values.
	
		@return	bool	Whether the column can contain null values.
	*/

	abstract public function isNullable();

	/**
		Returns the number of the column in the table.

		@return	int	The number of the column in the table.
	*/

	public function num()
	{
		return (int)$this->aData['num'];
	}
}
