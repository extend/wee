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
		Number of calls to the query method.
		For informational and debugging purpose only.
	*/

	protected $iNumQueries = 0;

	/**
		Initialize the driver and connects to the database.
		The arguments available may change between drivers.

		@param $aParams Arguments for database connection, identification, and class initialization
	*/

	abstract public function __construct($aParams = array());

	/**
		The database driver objects can't be cloned.
	*/

	private function __clone()
	{
	}

	/**
		Execute a batch of SQL queries.

		@param $aQueries The array of query strings
	*/

	public function batchQueries($aQueries)
	{
		foreach ($aQueries as $sQuery)
			$this->query($sQuery);
	}

	/**
		Common function for building queries that use named parameters placeholders.
		Used to replace all the named parameters in the query by the specified arguments, escaped as needed.

		@param	$aArguments	The query and the array of arguments passed to the query method
		@return	string		The query safely build
	*/

	public function bindNamedParameters($aArguments)
	{
		$sQueryString = $aArguments[0];

		$aMatches = array();
		preg_match_all('/:([\w_]+)/', $sQueryString, $aMatches);
		arsort($aMatches[1]);

		foreach ($aMatches[1] as $sName)
		{
			fire(!array_key_exists($sName, $aArguments[1]), 'DatabaseException',
				'Could not bind the named parameter for ' . $sName . ' because the value was not given in the arguments.');
			$sQueryString = str_replace(':' . $sName, $this->escape($aArguments[1][$sName]), $sQueryString);
		}

		return $sQueryString;
	}

	/**
		Common function for building queries that use question marks placeholders.
		Used to replace all the ? in the query by the specified arguments, escaped as needed.

		@param	$aArguments	The query and the arguments passed to the query method
		@return	string		The query safely built
	*/

	public function bindQuestionMarks($aArguments)
	{
		$aParts		= explode('?', $aArguments[0]);

		$iNbParts	= sizeof($aParts);
		$iNbArgs	= sizeof($aArguments);

		fire($iNbParts != $iNbArgs, 'UnexpectedValueException',
			'The number of placeholders in the query does not match the number of arguments.');

		$s = $aParts[0];
		for ($i = 1; $i < sizeof($aArguments); $i++)
			$s .= $this->escape($aArguments[$i]) . $aParts[$i];

		return $s;
	}

	/**
		Execute an SQL query.

		@param	$sQueryString		The query string
		@return	weeDatabaseResult	Only with SELECT queries: an object for results handling
	*/

	abstract protected function doQuery($sQueryString);

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
		Return the number of successfull queries.
		Only the queries executed using the query method are recorded.
		For informational and debugging purpose only.

		@return integer The number of queries since the creation of the class
	*/

	public function numQueries()
	{
		return $this->iNumQueries;
	}

	/**
		Prepare an SQL query statement.

		@param	$sQueryString			The query string.
		@return	weeDatabaseStatement	The prepared statement.
		@see weeDatabaseStatement
	*/

	abstract public function prepare($sQueryString);

	/**
		Build and execute an SQL query.

		If you pass other arguments to it, the arguments will be escaped and inserted into the query.

		For example if you have:
			weeApp()->db->query('SELECT * FROM example_table WHERE example_name=? AND example_id=? LIMIT 1', $sField, $iId);
		It will select the row with the $sField example_name and $iId example_id.

		You can also use named parameters. This can make for more readable queries,
		but more importantly you won't have to repeat variables when you pass them
		after the query string, since they will have a name assigned.

		There's two ways to use named parameters. You can assign explicit names, or use the implicit ones.
		If you specify names the above query will become like this:
			weeApp()->db->query('SELECT * FROM example_table WHERE example_name=:name AND example_id=:id LIMIT 1', array(
				'name'	=> $sField,
				'id'	=> $iId,
			));

		If you don't specify names, the array indexes will be used by default. Array indexes starts at 0.
		The example then becomes this:
			weeApp()->db->query(
				'SELECT * FROM example_table WHERE example_name=:0 AND example_id=:1 LIMIT 1',
				array($sField, $iId)
			);

		All data passed to it not required by the query will be ignored. You can thus pass a bigger array
		that contains what you need (like a POST array) and everything will be binded automatically and
		escaped as needed. Thus, you can choose the simplest method for writing your queries depending on
		what form your data is.

		@overload query($mQueryString, $mArg1, $mArg2, ...) Example of query call with multiple unnamed parameters
		@overload query($mQueryString, $aNamedParameters) Example of query call with named parameters
		@param	$mQueryString		The query string
		@param	...					The additional arguments that will be inserted into the query
		@return	weeDatabaseResult	Only with SELECT queries: an object for results handling
	*/

	public function query($mQueryString)
	{
		$this->iNumQueries++;

		if (func_num_args() > 1)
		{
			if (strpos($mQueryString, '?') !== false)
				$mQueryString = $this->bindQuestionMarks(func_get_args());
			else
				$mQueryString = $this->bindNamedParameters(func_get_args());
		}
		elseif (is_object($mQueryString))
			$mQueryString = $mQueryString->build($this);

		return $this->doQuery($mQueryString);
	}
}

?>
