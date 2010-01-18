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
	PostgreSQL specialization of weeDbMetaForeignKey.
*/

class weePgSQLDbMetaForeignKey extends weeDbMetaSchemaForeignKey
	implements weeDbMetaCommentable
{
	/**
		Initializes a new pgsql foreign key object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weePgSQLDbMetaTable.

		@param	$oMeta			The pgsql dbmeta object.
		@param	$aData			The foreign key data.
		@param	$oTable			The pgsql table of the foreign key.
	*/

	public function __construct(weePgSQLDbMeta $oMeta, array $aData, weePgSQLDbMetaTable $oTable)
	{
		parent::__construct($oMeta, $aData, $oTable);
	}

	/**
		Returns the names of the columns of the foreign key.

		The columns are ordered as specified in the definition of the foreign key.

		@return	array(string)	The names of the columns of the foreign key.
	*/

	public function columnsNames()
	{
		return $this->fetchColumnsNames($this->aData['table_oid'], $this->aData['columns']);
	}

	/**
		Returns the comment of the foreign key.

		@return	string			The comment of the foreign key.
	*/

	public function comment()
	{
		return $this->aData['comment'];
	}

	/**
		Fetches the names of a given set of columns.

		The names are returned in their order of appearance in the set.

		@param	$sTableOID		The OID of the table which columns belong to.
		@param	$sColumns		The set of columns, a text representation of a pgsql array with ',' as the element separator.
		@return	array(string)	The names of the columns.
	*/

	protected function fetchColumnsNames($sTableOID, $sColumns)
	{
		$oQuery = $this->db()->query("
			SELECT		attnum, attname
				FROM	pg_catalog.pg_attribute
				WHERE	attrelid	= ?
					AND	attnum		= ANY(CAST(string_to_array(?, ',') AS smallint[]))
		", $sTableOID, $sColumns);

		$aColumns = array();
		$aIndexes = array_flip(explode(',', $sColumns));
		foreach ($oQuery as $aColumn)
			$aColumns[$aIndexes[$aColumn['attnum']]] = $aColumn['attname'];
		ksort($aColumns);
		return $aColumns;
	}


	/**
		Returns the referenced columns of the foreign key.

		@return	array(string)	The names of the referenced columns of the foreign key.
	*/

	public function referencedColumnsNames()
	{
		return $this->fetchColumnsNames(
			$this->aData['referenced_table_oid'],
			$this->aData['referenced_columns']
		);
	}
}
