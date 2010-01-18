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
	PostgreSQL specialization of weeDbMetaTable.
*/

class weePgSQLDbMetaTable extends weeDbMetaTable
	implements weeDbMetaCommentable, weeDbMetaForeignKeyProvider, weeDbMetaSchemaObject
{
	/**
		Initializes a new pgsql table object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weePgSQLDbMetaSchema.

		@param	$oMeta						The pgsql dbmeta object.
		@param	$aData						The table data.
	*/

	public function __construct(weePgSQLDbMeta $oMeta, array $aData)
	{
		parent::__construct($oMeta, $aData);
	}

	/**
		Returns a column of the table.

		@param	$sName						The column name.
		@return	weePgSQLDbMetaColumn		The column.
		@throw	UnexpectedValueException	The column does not exist in the table.
	 */

	public function column($sName)
	{
		$oQuery = $this->meta()->db()->query('
			SELECT		n.nspname AS schema, c.relname AS table, a.attname AS name, a.attnum AS num,
						NOT a.attnotnull AS nullable, a.atthasdef AS has_default,
						pg_catalog.col_description(a.attrelid, a.attnum) AS comment,
						t.typname AS type, a.atttypmod AS type_mod
				FROM	pg_catalog.pg_attribute a
							JOIN pg_catalog.pg_class		c ON c.oid = a.attrelid
							JOIN pg_catalog.pg_namespace	n ON n.oid = c.relnamespace
							JOIN pg_catalog.pg_type			t ON t.oid = a.atttypid
				WHERE	a.attrelid		= CAST(? AS regclass)
					AND a.attname		= ?
					AND a.attnum		> 0
					AND	a.attisdropped	= false
				LIMIT	1
		', $this->quotedName(), $sName);

		count($oQuery) == 1
			or burn('UnexpectedValueException',
				sprintf(_WT('Column "%s" does not exist.'), $sName));

		return $this->instantiateObject($this->getColumnClass(), $oQuery->fetch());
	}

	/**
		Returns whether a given column exists in the table.

		@param	$sName						The column name.
		@return	bool						true if the column exists, false otherwise.
	*/

	public function columnExists($sName)
	{
		$sExists = $this->db()->queryValue('SELECT EXISTS(SELECT 1
				FROM pg_catalog.pg_attribute
				WHERE	attname			= :column
					AND attrelid		= CAST(:quoted AS regclass)
					AND attisdropped	= false
		)', array('column' => $sName, 'quoted' => $this->quotedName()));

		return $sExists == 't';
	}

	/**
		Returns the comment of the table.

		@return	string						The comment of the table.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Returns a foreign key of a given name.

		@param	$sName						The name of the foreign key.
		@return	weePgSQLDbMetaPrimaryKey	The foreign key.
		@throw	UnexpectedValueException	The foreign key does not exist.
	*/

	public function foreignKey($sName)
	{
		$oQuery = $this->db()->query("
			SELECT		n.nspname AS schema, cl.relname AS table, co.conname AS name,
						pg_catalog.obj_description(co.oid, 'pg_constraint') AS comment,
						array_to_string(co.conkey, ',') AS columns,
						nf.nspname AS referenced_schema,
						clf.relname AS referenced_table,
						array_to_string(co.confkey, ',') AS referenced_columns,
						co.conrelid AS table_oid, co.confrelid AS referenced_table_oid
				FROM	pg_catalog.pg_constraint co
							JOIN pg_catalog.pg_class		cl	ON cl.oid	= co.conrelid
							JOIN pg_catalog.pg_class		clf	ON clf.oid	= co.confrelid
							JOIN pg_catalog.pg_namespace	n	ON n.oid	= co.connamespace
							JOIN pg_catalog.pg_namespace	nf	ON nf.oid	= clf.relnamespace
				WHERE	co.conrelid	= CAST(? AS regclass)
					AND	co.conname	= ?
					AND	co.contype	= 'f'
				LIMIT	1
		", $this->quotedName(), $sName);

		count($oQuery) == 1
			or burn('UnexpectedValueException',
				sprintf(_WT('Foreign key "%s" does not exist.'), $sName));

		return $this->instantiateObject($this->getForeignKeyClass(), $oQuery->fetch());
	}

	/**
		Returns whether a foreign key of a given name.

		@param	$sName						The name of the foreign key.
		@return	bool						Whether the foreign key exists.
	*/

	public function foreignKeyExists($sName)
	{
		$sExists = $this->db()->queryValue("SELECT EXISTS(SELECT 1
			FROM pg_catalog.pg_constraint
			WHERE conrelid = CAST(? AS regclass) AND conname = ? AND contype = 'f'
		)", $this->quotedName(), $sName);
		return $sExists == 't';
	}

	/**
		Returns all the foreign keys.

		@return	array(weePgSQLDbMetaPrimaryKey)	The array of foreign keys.
	*/

	public function foreignKeys()
	{
		$oQuery = $this->db()->query("
			SELECT			n.nspname AS schema, cl.relname AS table, co.conname AS name,
							pg_catalog.obj_description(co.oid, 'pg_constraint') AS comment,
							array_to_string(co.conkey, ',') AS columns,
							nf.nspname AS referenced_schema,
							clf.relname AS referenced_table,
							array_to_string(co.confkey, ',') AS referenced_columns,
							co.conrelid AS table_oid, co.confrelid AS referenced_table_oid
				FROM		pg_catalog.pg_constraint co
								JOIN pg_catalog.pg_class		cl	ON cl.oid	= co.conrelid
								JOIN pg_catalog.pg_class		clf	ON clf.oid	= co.confrelid
								JOIN pg_catalog.pg_namespace	n	ON n.oid	= co.connamespace
								JOIN pg_catalog.pg_namespace	nf	ON nf.oid	= clf.relnamespace
				WHERE		co.conrelid	= CAST(? AS regclass)
						AND	co.contype	= 'f'
				ORDER BY	co.conname
		", $this->quotedName());

		$aForeignKeys	= array();
		$sClass			= $this->getForeignKeyClass();
		foreach ($oQuery as $aForeignKey)
			$aForeignKeys[] = $this->instantiateObject($sClass, $aForeignKey);
		return $aForeignKeys;
	}

	/**
		Returns the name of the column class.

		@return	string						The name of the column class.
	*/

	public function getColumnClass()
	{
		return 'weePgSQLDbMetaColumn';
	}

	/**
		Returns the name of the foreign key class.

		@return	string						The name of the foreign key class.
	*/

	public function getForeignKeyClass()
	{
		return 'weePgSQLDbMetaForeignKey';
	}

	/**
		Returns the name of the primary key class.

		@return	string						The name of the primary key class.
	*/

	public function getPrimaryKeyClass()
	{
		return 'weePgSQLDbMetaPrimaryKey';
	}

	/**
		Returns whether the table has a primary key.

		@return	bool						true if the table has a primary key, false otherwise.
	*/

	public function hasPrimaryKey()
	{
		$sExists = $this->db()->queryValue("SELECT EXISTS(SELECT 1
			FROM pg_catalog.pg_constraint
			WHERE conrelid = CAST(? AS regclass) AND contype = 'p'
		)", $this->quotedName());
		return $sExists == 't';
	}

	/**
		Returns the quoted schema-qualified name of the table.

		@return	string						The quoted schema-qualified name of the table.
	*/

	public function quotedName()
	{
		return $this->db()->escapeIdent($this->schemaName()) . '.' . parent::quotedName();
	}

	/**
		Returns the primary key of the table.

		The columns are ordered as specified in the definition of the primary key.

		@return	weePgSQLDbMetaPrimaryKey	The primary key.
		@throw	IllegalStateException		The table does not have a primary key.
	*/

	public function primaryKey()
	{
		$oQuery = $this->db()->query("
			SELECT		n.nspname AS schema, cl.relname AS table, co.conname AS name,
						pg_catalog.obj_description(co.oid, 'pg_constraint') AS comment
				FROM	pg_catalog.pg_constraint co
						JOIN pg_catalog.pg_class		cl	ON cl.oid	= co.conrelid
						JOIN pg_catalog.pg_namespace	n	ON n.oid	= co.connamespace
				WHERE	co.conrelid	= CAST(? AS regclass)
					AND	co.contype	= 'p'
				LIMIT	1
		", $this->quotedName());

		count($oQuery) == 1
			or burn('IllegalStateException',
				_WT('The table does not have a primary key.'));

		return $this->instantiateObject($this->getPrimaryKeyClass(), $oQuery->fetch());
	}

	/**
		Queries all the columns of the table.

		@return	weePgSQLResult				The data of all the columns of the table.
	*/

	protected function queryColumns()
	{
		return $this->meta()->db()->query('
			SELECT			n.nspname AS schema, c.relname AS table, a.attname AS name, a.attnum AS num,
							NOT a.attnotnull AS nullable, a.atthasdef AS has_default,
							pg_catalog.col_description(a.attrelid, a.attnum) AS comment,
							t.typname AS type, a.atttypmod AS type_mod
				FROM		pg_catalog.pg_attribute a
								JOIN pg_catalog.pg_class		c ON c.oid = a.attrelid
								JOIN pg_catalog.pg_namespace	n ON n.oid = c.relnamespace
								JOIN pg_catalog.pg_type			t ON t.oid = a.atttypid
				WHERE		a.attrelid		= CAST(? AS regclass)
						AND a.attnum		> 0
						AND	a.attisdropped	= false
				ORDER BY	a.attnum
		', $this->quotedName());
	}

	/**
		Returns the name of the schema of the table.

		@return	string						The name of the schema of the table.
	*/

	public function schemaName()
	{
		return $this->aData['schema'];
	}
}
