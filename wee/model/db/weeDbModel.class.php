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

		@return weeDatabase The database associated to this model.
	*/

	public function getDb()
	{
		fire(empty($this->oDatabase) && !is_callable('weeApp'), 'IllegalStateException',
			'No database has been associated to this model.');

		return (empty($this->oDatabase) ? weeApp()->db : $this->oDatabase);
	}

	/**
		Associate a database to this model.

		@param $oDb weeDatabase The database instance to associate to this model.
		@return $this
	*/

	public function setDb($oDb)
	{
		$this->oDatabase = $oDb;
		return $this;
	}
}
