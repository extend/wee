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
	PostgreSQL specialization of weeDbMeta.
*/

class weePgSQLDbMeta extends weeDbMeta
	implements weeDbMetaSchemaProvider
{
	/**
		The DBMS handled by this class (pgsql).
	*/

	protected $mDBMS = 'pgsql';

	/**
		Returns the current schema of the database.

		@return	weePgSQLDbMetaSchema		The current schema.
	*/

	public function currentSchema()
	{
		$a = $this->db()->query("
			SELECT		nspname AS name, pg_catalog.pg_get_userbyid(nspowner) AS owner, oid,
						pg_catalog.pg_has_role(nspowner, 'MEMBER') AS alterable,
						pg_catalog.obj_description(oid, 'pg_namespace') AS comment
				FROM	pg_namespace
				WHERE	nspname	= current_schema()
				LIMIT	1
		")->fetch();

		$sClass = $this->getSchemaClass();
		return new $sClass($this, $a);
	}

	/**
		Returns the name of the schema class.

		@return	string						The name of the schema class.
	*/

	public function getSchemaClass()
	{
		return 'weePgSQLDbMetaSchema';
	}

	/**
		Returns the name of the table class.

		@return	string						The name of the table class.
	*/

	public function getTableClass()
	{
		return 'weePgSQLDbMetaTable';
	}

	/**
		Returns a schema of a given name in the database.

		@param	$sName						The name of the schema.
		@return	weePgSQLDbMetaSchema		The schema.
		@throw	UnexpectedValueException	The schema does not exist.
	*/

	public function schema($sName)
	{
		$oQuery = $this->db()->query("
			SELECT		nspname AS name, pg_catalog.pg_get_userbyid(nspowner) AS owner, oid,
						pg_catalog.pg_has_role(nspowner, 'MEMBER') AS alterable,
						pg_catalog.obj_description(oid, 'pg_namespace') AS comment
				FROM	pg_namespace
				WHERE	nspname	= ?
				LIMIT	1
		", $sName);

		count($oQuery) == 1
			or burn('UnexpectedValueException',
				sprintf(_WT('Schema "%s" does not exist in the database.'), $sName));

		$sClass = $this->getSchemaClass();
		return new $sClass($this, $oQuery->fetch());
	}

	/**
		Returns whether a schema of a given name exists in the database.

		@param	$sName						The name of the schema.
		@return	bool						true if the schema exists in the database, false otherwise.
	*/

	public function schemaExists($sName)
	{
		$sExists = $this->db()->queryValue('SELECT EXISTS(SELECT 1
			FROM pg_catalog.pg_namespace
			WHERE nspname = ?
		)', $sName);
		return $sExists == 't';
	}

	/**
		Returns all the schemas of the database.

		@return	array(weePgSQLDbMetaSchema)	The array of schemas.
	*/

	public function schemas()
	{
		$aSchemas	= array();
		$sClass		= $this->getSchemaClass();
		foreach ($this->querySchemas() as $aSchema)
			$aSchemas[] = new $sClass($this, $aSchema);
		return $aSchemas;
	}

	/**
		Returns the names of all the schemas in the database.

		@return	array(string)	The names of all the schemas.
	*/

	public function schemasNames()
	{
		$aSchemas	= array();
		$sClass		= $this->getSchemaClass();
		foreach ($this->querySchemas() as $aSchema)
			$aSchemas[] = $aSchema['name'];
		return $aSchemas;
	}

	/**
		Returns a visible table of a given name in the database.

		@param	$sName						The name of the table.
		@return weePgSQLDbMetaTable			The table.
		@throw	UnexpectedValueException	The schema does not exist.
	*/

	public function table($sName)
	{
		$oQuery = $this->db()->query("
			SELECT		n.nspname AS schema, c.relname AS name, r.rolname AS owner,
						pg_catalog.obj_description(c.oid, 'pg_class') AS comment,
						pg_catalog.pg_has_role(r.rolname, 'USAGE') AS alterable
				FROM	pg_catalog.pg_class c
						JOIN pg_catalog.pg_namespace	n ON n.oid = c.relnamespace
						JOIN pg_catalog.pg_roles		r ON r.oid = c.relowner
				WHERE		c.relname = ?
						AND c.relkind = 'r'
						AND pg_table_is_visible(c.oid)
				LIMIT	1
		", $sName);

		count($oQuery) == 1 or burn('UnexpectedValueException',
			sprintf(_WT('Table "%s" does not exist in the database or is not in its current schema search path.'), $sName));

		$sClass = $this->getTableClass();
		return new $sClass($this, $oQuery->fetch());
	}

	/**
		Returns whether a visible table of a given name exists in the database.

		@param	$sName						The name of the table.
		@return	bool						true if the table exists in the schema, false otherwise.
	*/

	public function tableExists($sName)
	{
		$sExists = $this->db()->queryValue("SELECT EXISTS(SELECT 1
			FROM pg_catalog.pg_class
			WHERE relname = ? AND relkind = 'r' AND pg_table_is_visible(oid)
		)", $sName);
		return $sExists == 't';
	}

	/**
		Queries all the schemas of the database.

		@return	weeDatabaseResult	The data of all the schemas of the database.
	*/

	protected function querySchemas()
	{
		return $this->db()->query("
			SELECT			nspname AS name, pg_catalog.pg_get_userbyid(nspowner) AS owner, oid,
							pg_catalog.pg_has_role(nspowner, 'MEMBER') AS alterable,
							pg_catalog.obj_description(oid, 'pg_namespace') AS comment
				FROM		pg_namespace
				ORDER BY	nspname
		");
	}

	/**
		Queries all the visible tables of the database.

		@return	weeDatabaseResult	The data of all the tables of the database.
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
				WHERE		c.relkind = 'r'
						AND pg_table_is_visible(c.oid)
				ORDER BY	schema, name
		");
	}
}
