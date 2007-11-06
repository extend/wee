<?php

/*
	Web:Extend
	Copyright (c) 2007 Dev:Extend

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
*/

abstract class weeDatabaseStatement
{
	/**
		Link resource to the database where statements will be prepared and executed.
	*/

	protected $rLink;

	/**
		Create a prepared statement.

		@param $mQueryString The query string
	*/

	public function __construct($rLink, $sQueryString)
	{
		$this->rLink = $rLink;
	}

	/**
		The database driver objects can't be cloned.
	*/

	private function __clone()
	{
	}

	/**
		Bind parameters to the statement.

		You can pass either one parameter name and its value,
		or an array of parameters that will be binded.

		@overload bind($sName, $sValue) Example of query call with one parameter instead of an array
		@param	$aParameters		The parameters to bind to the statement
		@return	$this
	*/

	abstract public function bind($aParameters);

	/**
		Execute the prepared statement.

		@return weeDatabaseResult Only with SELECT queries: an object for results handling
	*/

	abstract public function execute();

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.

		@return integer The number of affected rows in the last query
	*/

	abstract public function numAffectedRows();
}

?>
