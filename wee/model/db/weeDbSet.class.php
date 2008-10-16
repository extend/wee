<?php

/*
	Web:Extend
	Copyright (c) 2008 Dev:Extend

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
	Base class for defining a set of rows for a database table.
*/

abstract class weeDbSet extends weeSet
{
	/**
		The database this set is associated to.
		Defaults to weeApp()->db.
	*/

	protected $oDatabase;

	/**
		Returns the database associated to this set.

		@return		weeDatabase										The database associated to this set.
	*/

	public function getDb()
	{
		fire(empty($this->oDatabase) && !is_callable('weeApp'), 'IllegalStateException',
			'No database has been associated to this set.');

		return (empty($this->oDatabase) ? weeApp()->db : $this->oDatabase);
	}

	/**
		Builds and executes a SQL query.

		This method is a shortcut to the following idiom:
			$this->getDb()->query(...)->rowClass($this->sModel);

		If the query returned a result set, it is automatically associated to the model
		of this class.

		@overload	query($mQueryString, $mArg1, $mArg2, ...)		Example of query call with multiple unnamed parameters
		@overload	query($mQueryString, $aNamedParameters)			Example of query call with named parameters
		@param		$mQueryString									The query string
		@param		...												The additional arguments that will be inserted into the query
		@return		mixed											An instance of weeDatabaseResult or null.
		@see		weeDatabase::query
	*/

	protected function query($mQueryString)
	{
		$aArgs	= func_get_args();
		$m		= call_user_func_array(array($this->getDb(), 'query'), $aArgs);

		if ($m instanceof weeDatabaseResult && $this->sModel !== null)
			$m->rowClass($this->sModel);
		return $m;
	}

	/**
		Fetches a row from the database.

		This method executes a given SQL query and returns an instance of the model
		of this set.

		@overload	queryRow($mQueryString, $mArg1, $mArg2, ...)	Example of query call with multiple unnamed parameters.
		@overload	queryRow($mQueryString, $aNamedParameters)		Example of query call with named parameters.
		@param		$mQueryString									The query string.
		@param		...												The additional arguments that will be inserted into the query.
		@return		object											An instance of the model of this set.
		@throw		UnexpectedValueException						The SQL query did not return a result set.
		@throw		UnexpectedValueException						The result set does not contain exactly one row.
		@see		weeDatabase::query
	*/

	protected function queryRow($mQueryString)
	{
		$a = func_get_args();
		$m = call_user_func_array(array($this, 'query'), $a);

		$m instanceof weeDatabaseResult
			or burn('UnexpectedValueException',
				_('The SQL query did not return a result set.'));

		count($m) == 1
			or burn('UnexpectedValueException',
				_('The result set does not contain exactly one row.'));

		return $m->fetch();
	}

	/**
		Build and execute an SQL value query.

		This method is a shortcut to $this->getDb()->queryValue(...).

		@overload	queryValue($mQueryString, $mArg1, $mArg2, ...)	Example of query call with multiple unnamed parameters
		@overload	queryValue($mQueryString, $aNamedParameters)	Example of query call with named parameters
		@param		$mQueryString									The query string
		@param		...												The additional arguments that will be inserted into the query
		@return		mixed											The queried value.
		@see		weeDatabase::queryValue
	*/

	protected function queryValue($mQueryString)
	{
		$a = func_get_args();
		return call_user_func_array(array($this->getDb(), 'queryValue'), $a);
	}

	/**
		Associate a database to this set.

		@param		$oDb											The database instance to associate to this set.
		@return		$this											Used to chain methods.
	*/

	public function setDb($oDb)
	{
		$this->oDatabase = $oDb;
		return $this;
	}
}
