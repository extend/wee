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
	Encapsulate weePgSQLDatabase and run EXPLAIN on the queries sent to it.

	@warning Experimental.
*/

class weeExplainPgSQLDatabase extends weePgSQLDatabase
{
	/**
		Run an EXPLAIN query instead of the normal query given in parameter.
		Queries will fail if used with anything other than SELECT.

		@param	$sQueryString	The query string
		@return	weePgSQLResult	Object containing the EXPLAIN results.
	*/

	protected function doQuery($sQueryString)
	{
		return new weeExplainSQLResult(parent::doQuery('EXPLAIN ' . $sQueryString));
	}
}
