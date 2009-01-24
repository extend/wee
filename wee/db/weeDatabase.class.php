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
	Base class for database handling.
	Defines the required elements for all the database drivers.
*/

abstract class weeDatabase
{
	/**
		The meta object associated with the database
	*/

	protected $oMeta;

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

	private final function __clone()
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

	protected function bindNamedParameters($aArguments)
	{
		$sQueryString = $aArguments[0];

		$aMatches = array();
		preg_match_all('/:([\w_]+)/', $sQueryString, $aMatches);
		arsort($aMatches[1]);

		foreach ($aMatches[1] as $sName)
		{
			// see http://blog.extend.ws/2008/03/01/arrayaccess-quirks/
			is_array($aArguments[1]) ? array_key_exists($sName, $aArguments[1]) : isset($aArguments[1][$sName])
				or burn('InvalidArgumentException',
					sprintf(_WT('Could not bind the parameter "%s" because its value was not given in the arguments.'), $sName));

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

	protected function bindQuestionMarks($aArguments)
	{
		$aParts		= explode('?', $aArguments[0]);

		$iNbParts	= sizeof($aParts);
		$iNbArgs	= sizeof($aArguments);

		$iNbParts == $iNbArgs or burn('UnexpectedValueException',
			_WT('The number of placeholders in the query does not match the number of arguments.'));

		$s = $aParts[0];
		for ($i = 1; $i < sizeof($aArguments); $i++)
			$s .= $this->escape($aArguments[$i]) . $aParts[$i];

		return $s;
	}

	/**
		Does the database-dependent logic of the escape operation.

		@param	$mValue	The value to escape.
		@return	string	The escaped value.
	*/

	abstract protected function doEscape($mValue);

	/**
		Execute an SQL query.

		@param	$sQueryString		The query string
		@return	weeDatabaseResult	Only with SELECT queries: an object for results handling
	*/

	abstract protected function doQuery($sQueryString);

	/**
		Escapes the given value for safe concatenation in an SQL query.
		You should not build query by concatenation if possible (see query).
		You should NEVER use sprintf when building queries.

		When the given value is null, the SQL token "null" is returned.

		@param	$mValue	The value to escape
		@return	string	The escaped value.
	*/

	public function escape($mValue)
	{
		if ($mValue === null)
			return 'null';

		if ($mValue instanceof Printable)
			$mValue = $mValue->toString();
		elseif (is_float($mValue))
		{
			$sFormerLocale = setlocale(LC_NUMERIC, 'C');
			$mValue = (string)$mValue;
			setlocale(LC_NUMERIC, $sFormerLocale);
		}

		return $this->doEscape($mValue);
	}

	/**
		Escapes the given identifier for safe concatenation in an SQL query.

		@param	$sValue	The identifier to escape
		@return	string	The escaped identifier, wrapped around adequate quotes
	*/

	abstract public function escapeIdent($sValue);

	/**
		Returns the name of the dbmeta class associated with this driver.

		@param	mixed	The name of the dbmeta class or null if the driver does not support dbmeta.
	*/

	public function getMetaClass()
	{
		return null;
	}

	/**
		Returns the primary key index value.
		Useful when you need to retrieve the row primary key value you just inserted.
		This function may work a bit differently in each drivers.

		@param	$sName	The primary key index name, if needed
		@return	integer	The primary key index value
	*/

	abstract public function getPKId($sName = null);

	/**
		Returns the meta object associated with this database.

		@return weeDbMeta				The meta object.
		@throw	BadMethodCallException	This database driver does not support dbmeta.
	*/

	public function meta()
	{
		if ($this->oMeta === null)
		{
			$sClass = $this->getMetaClass();
			$sClass !== null or burn('BadMethodCallException',
				_WT('This database driver does not support dbmeta.'));
			$this->oMeta = new $sClass($this);
		}

		return $this->oMeta;
	}

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
		Prepares an SQL query statement.

		By default, returns an instance of weeDatabaseDummyStatement,
		which is a fallback fake implementation of prepared statements.

		When possible, a real weeDatabaseStatement subclass should be
		written.

		@param	$sQuery						The query string.
		@return	weeDatabaseDummyStatement	The prepared statement.
	*/

	public function prepare($sQuery)
	{
		return new weeDatabaseDummyStatement($this, $sQuery);
	}

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

		return $this->doQuery($mQueryString);
	}

	/**
		Build and execute an SQL value query.

		This method is a shortcut to the following idiom:
			$a = $this->query('SELECT count(*) FROM example_table')->fetch();
			return array_shift($a);

		An UnexpectedValueException will be thrown if the query did not return exactly one row or if the row does
		not contain exactly one column.

		@overload query($mQueryString, $mArg1, $mArg2, ...) Example of query call with multiple unnamed parameters
		@overload query($mQueryString, $aNamedParameters) Example of query call with named parameters
		@param	$mQueryString		The query string
		@param	...					The additional arguments that will be inserted into the query
		@see	query($mQueryString)
	*/

	public function queryValue($mQueryString)
	{
		$aArgs	= func_get_args();
		$m		= call_user_func_array(array($this, 'query'), $aArgs);

		$m instanceof weeDatabaseResult or burn('InvalidArgumentException', _WT('The query is not a SELECT query.'));
		count($m) == 1 or burn('UnexpectedValueException', _WT('The query did not return exactly one row.'));

		$a = $m->fetch();
		count($a) == 1 or burn('UnexpectedValueException', _WT('The queried row does not contain exactly one column.'));

		// Small test to allow weeExplainSQLResult to work correctly with this method
		if (is_array($a))
			return array_shift($a);
		return $a;
	}
}
