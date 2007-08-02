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
	Base class for database query results handling.
	An object of this class is created by the weeDatabase's query method for SELECT statements.
*/

abstract class weeDatabaseResult implements Countable, Iterator
{
	/**
		Wether we are in the template and must encode the results.
	*/

	protected $bEncodeResults = false;

	/**
		The class used to return row's data.
		If empty, an array will be returned.
	*/

	protected $sRowClass;

	/**
		Initialize the class with the result of the query.

		@param $rResult The resource for the query result returned by weeDatabase's query method.
	*/

	abstract public function __construct($rResult);

	/**
		Used by weeTemplate to automatically encode row results.

		@return $this
	*/

	public function encodeResults()
	{
		$this->bEncodeResults = true;
		return $this;
	}

	/**
		Fetch the next row.

		Usually used to fetch the result of a query with only one result returned,
		because if there's no result it throws an exception.

		The return value type can differ depending on the row class.
		The row class can be changed using the rowClass method.

		@return array Usually an array, or a child of weeDatabaseRow.
	*/

	abstract public function fetch();

	/**
		Fetch all the rows returned by the query.

		The return value type can differ depending on the row class.
		The row class can be changed using the rowClass method.

		@return array Usually an array, or a child of weeDatabaseRow.
	*/

	abstract public function fetchAll();

	/**
		Encodes the row if needed.

		@param	$aRow	The data row.
		@return	array	The data row encoded, if applicable.
	*/

	protected function processRow($aRow)
	{
		if ($this->bEncodeResults)
		{
			if ($aRow instanceof weeDatabaseRow)
				return $aRow->encodeResults();

			return weeOutput::encodeArray($aRow);
		}

		return $aRow;
	}

	/**
		Change the type of the return for fetch and fetchAll methods.

		By default they return an array containing the row values,
		but a child class of weeDatabaseRow can be specified that will be used
		to create objects containing the row values.

		This can be used after a query if you want to abstract your result in
		an object and add methods for easy manipulation of this result.

		@param	$sClass The class used to return row's data.
		@return	$this
	*/

	public function rowClass($sClass)
	{
		fire(empty($sClass), 'InvalidParameterException');

		$this->sRowClass = $sClass;
		return $this;
	}
}

?>
