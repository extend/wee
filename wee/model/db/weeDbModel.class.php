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
	Base class for defining a model for a database table.
*/

abstract class weeDbModel extends weeModel
{
	/**
		The database this model is associated to.
		Defaults to weeApp()->db.
	*/

	protected $oDatabase;

	/**
		Returns the database associated to this model.

		@return	weeDatabase										The database associated to this model.
		@throw	IllegalStateException							No database has been associated to this model.
	*/

	public function getDb()
	{
		$this->oDatabase !== null or is_callable('weeApp')
			or burn('IllegalStateException',
				_('No database has been associated to this model.'));

		return $this->oDatabase === null ? weeApp()->db : $this->oDatabase;
	}

	/**
		Builds and executes a SQL query.

		@overload	query($mQueryString, $mArg1, $mArg2, ...)	Example of query call with multiple unnamed parameters
		@overload	query($mQueryString, $aNamedParameters)		Example of query call with named parameters
		@param		$mQueryString								The query string
		@param		...											The additional arguments that will be inserted into the query
		@return		mixed										An instance of weeDatabaseResult or null.
		@see		weeDatabase::query
	*/

	protected function query($mQueryString)
	{
		$aArgs = func_get_args();
		return call_user_func_array(array($this->getDb(), 'query'), $aArgs);
	}


	/**
		Associate a database to this model.

		@param	$oDb											The database instance to associate to this model.
		@return	$this											Used to chain methods.
	*/

	public function setDb(weeDatabase $oDb)
	{
		$this->oDatabase = $oDb;
		return $this;
	}
}
