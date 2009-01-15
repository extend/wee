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
	Base class used to query meta data about database objects.
*/

abstract class weeDbMetaObject
{
	/**
		The database to query.
	*/

	private $oMeta;

	/**
		The database object data.
	*/

	protected $aData;

	/**
		Initializes a new database object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeDbMeta
		and the other classes of the dbmeta component.

		@param	$oMeta		The dbmeta object.
		@param	$aData		The object data.
	*/

	public function __construct(weeDbMeta $oMeta, array $aData)
	{
		$this->oMeta = $oMeta;
		$this->aData = $aData;
	}

	/**
		Returns the associated database object.

		@return	weeDatabase	The associated database object.
	*/

	public function db()
	{
		return $this->meta()->db();
	}

	/**
		Returns the dbmeta object of this database object.

		@return	weeDbMeta	The dbmeta object.
	*/

	public function meta()
	{
		return $this->oMeta;
	}

	/**
		Returns the name of the database object.

		@return	string		The name of the database object.
	*/

	public function name()
	{
		return $this->aData['name'];
	}

	/**
		Returns the quoted name of the database object.

		@return	string		The quoted name.
	*/

	public function quotedName()
	{
		return $this->db()->escapeIdent($this->name());
	}
}
