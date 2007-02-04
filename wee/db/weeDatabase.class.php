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
	Base class for database handling.
	Defines the required elements for all the database drivers.
*/

abstract class weeDatabase
{
	/**
		Initialize the driver and connects to the database.
		The arguments available may change between drivers.

		@param $aParams Arguments for database connection, identification, and class initialization
	*/

	abstract public function __construct($aParams = array());

	/**
		Closes the connection to the database.
	*/

	abstract public function __destruct();

	/**
		The database driver objects can't be cloned.
	*/

	private function __clone()
	{
	}

	/**
		Common function for building queries.
		Used to replace all the ? in the query by the specified arguments, escaped as needed.

		@param	$aArguments	The query and the arguments passed to the query method
		@return	string		The query safely build
	*/

	protected function buildSafeQuery($aArguments)
	{
		$aParts		= explode('?', $aArguments[0]);

		$iNbParts	= sizeof($aParts);
		$iNbArgs	= sizeof($aArguments);

		fire($iNbParts != $iNbArgs && $iNbParts - 1 != $iNbArgs, 'UnexpectedValueException');

		$q = null;
		for ($i = 1; $i < sizeof($aArguments); $i++)
			$q .= $aParts[$i - 1] . $this->escape($aArguments[$i]);

		if ($iNbParts == $iNbArgs)
			$q .= $aParts[$iNbParts - 1];

		return $q;
	}

	/**
		Escape the given value for safe concatenation in an SQL query.
		You should not build query by concatenation if possible (see query).
		You should NEVER use sprintf when building queries.

		@param	$mValue	The value to escape
		@return	string	The escaped value, wrapped around simple quotes
	*/

	abstract public function escape($mValue);

	/**
		Gets the last error the database returned.
		The drivers usually throw an exception when there's an error,
		but you can get the error if you catch the exception and then call this method.

		@return string The last error the database encountered
	*/

	abstract public function getLastError();

	/**
		Returns the primary key index value.
		Useful when you need to retrieve the row primary key value you just inserted.
		This function may work a bit differently in each drivers.

		@param	$sName	The primary key index name, if needed
		@return	integer	The primary key index value
	*/

	abstract public function getPKId($sName = null);

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return integer The number of affected rows in the last query
	*/

	abstract public function numAffectedRows();

	/**
		Returns the number of successfull queries.
		Only the queries executed using the query method are recorded.
		For informational and debugging purpose only.

		@return integer The number of queries since the creation of the class
	*/

	abstract public function numQueries();

	/**
		Execute an SQL query.

		If you pass other arguments to it, the arguments will be escaped and inserted into the query,
		using the buildSafeQuery method.

		For example if you have:
			$Db->query('SELECT ? FROM example_table WHERE example_id=? LIMIT 1', $sField, $iId);
		It will select the $sField field from the row with the $iId example_id.

		@overload query($mQueryString, $mArg1, $mArg2, ...) Example of query call with multiple arguments
		@param	$mQueryString		The query string
		@param	...					The additional arguments that will be inserted into the query
		@return	weeDatabaseResult	Only with SELECT queries: an object for results handling
	*/

	abstract public function query($mQueryString);
}

?>
