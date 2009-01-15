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
	PostgreSQL specialization of weeDbMetaSchema.
*/

class weePgSQLDbMetaSchema extends weeDbMetaSchema
	implements weeDbMetaCommentable, weeDbMetaTableProvider
{
	/**
		Initializes a new pgsql schema object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weePgSQLDbMeta.

		@param	$oMeta						The pgsql dbmeta object.
		@param	$aData						The schema data.
	*/

	public function __construct(weePgSQLDbMeta $oMeta, array $aData)
	{
		parent::__construct($oMeta, $aData);
	}

	/**
		Returns the comment of the schema.

		@return	string						The comment of the schema.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Returns a table of a given name in the schema.

		@param	$sName						The name of the table.
		@return	weePgSQLDbMetaTable			The table.
		@throw	UnexpectedValueException	The table does not exist in the schema.
	*/

	public function table($sName)
	{
		$oQuery = $this->meta()->db()->query("
			SELECT		n.nspname AS schema, c.relname AS name, r.rolname AS owner,
						pg_catalog.obj_description(c.oid, 'pg_class') AS comment,
						pg_catalog.pg_has_role(r.rolname, 'USAGE') AS alterable
			    FROM	pg_catalog.pg_class c
						JOIN pg_catalog.pg_namespace	n ON n.oid = c.relnamespace
						JOIN pg_catalog.pg_roles		r ON r.oid = c.relowner
				WHERE		c.relname = :table
						AND	c.relkind = 'r'
						AND	n.nspname = :name
				LIMIT	1
		", array('table' => $sName) + $this->aData);

		count($oQuery) == 1
			or burn('UnexpectedValueException',
				sprintf(_WT('Table "%s" does not exist in the schema.'), $sName));

		$sClass = $this->meta()->getTableClass();
		return new $sClass($this->meta(), $oQuery->fetch());
	}

	/**
		Returns whether a table of a given name exists in the schema.

		@param	$sName						The name of the table.
		@return	bool						true if the table exists in the schema, false otherwise.
	*/

	public function tableExists($sName)
	{
		$sExists = $this->db()->queryValue('SELECT EXISTS(SELECT 1
				FROM pg_catalog.pg_tables
				WHERE tablename = ? AND schemaname = ?
		)', $sName, $this->name());
		return $sExists == 't';
	}

	/**
		Queries all the tables in the schema.

		@return	weeDatabaseResult	The data of all the tables in the schema.
	*/

	protected function queryTables()
	{
		return $this->db()->query("
			SELECT			n.nspname AS schema, c.relname AS name, r.rolname AS owner,
							pg_catalog.obj_description(c.oid, 'pg_class') AS comment,
							pg_catalog.pg_has_role(r.rolname, 'USAGE') AS alterable
				FROM		pg_catalog.pg_class c
								JOIN pg_catalog.pg_namespace	n ON n.oid = c.relnamespace
								JOIN pg_catalog.pg_roles		r ON r.oid = c.relowner
				WHERE		n.nspname = ?
						AND	c.relkind = 'r'
				ORDER BY	c.relname
		", $this->name());
	}
}
