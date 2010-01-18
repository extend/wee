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
	Base class for prepared statements.

	Instances of this class are returned by weeDatabase's prepare method and
	should not be instantiated manually.
*/

abstract class weeDatabaseStatement
{
	/**
		Prepared statements cannot be cloned.
	*/

	private final function __clone()
	{
	}

	/**
		Does the database-dependent work to bind the parameters to the statement.

		The parameters are given as an associative array matching either index or
		names to parameters values, depending on whether the query is using
		interrogation marks placeholders.

		@param	$aParameters	The parameters to bind.
	*/

	abstract protected function doBind($aParameters);

	/**
		Binds parameters to the statement.

		If the query is not using interrogation marks placeholders,
		you can call this method with a parameter name and its value.

		@overload	bind($sName, $mValue)		Example of query call with one argument instead of an array.
		@param		$aParameters				The parameters to bind to the statement.
		@return		$this						Used to chain methods.
		@throw		InvalidArgumentException	The bind method has been called with one argument but it's not an array.
		@throw		InvalidArgumentException	The bind method has been called with two arguments but its first is not a string.
		@throw		BadMethodCallException		The bind method has been called with more than 2 arguments.
	*/

	public function bind($aParameters)
	{
		if (func_num_args() > 1)
		{
			is_string($aParameters) or burn('InvalidArgumentException',
				_WT('The first argument of the bind method should be a string when called with two parameters.'));

			$aParameters = array($aParameters => func_get_arg(1));
		}
		else
			is_array($aParameters) or burn('InvalidArgumentException',
				_WT('The given argument of the bind method is not an array.'));

		$this->doBind($aParameters);
		return $this;
	}

	/**
		Executes the prepared statement.

		@return	weeDatabaseResult	Only with SELECT queries: an object for results handling
	*/

	abstract public function execute();

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return	int	The number of affected rows in the last query.
	*/

	abstract public function numAffectedRows();
}
