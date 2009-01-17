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
	SQLite specialisation of weeDbMetaTable.
*/

class weeSQLiteDbMetaTable extends weeDbMetaTable
	implements weeDbMetaForeignKeyProvider
{
	/**
		The columns of the table.

		The only way to get the list of the columns of an SQLite
		table is through the PRAGMA table_info command, we
		cannot get informations about individual columns.
	*/

	protected $aColumns;

	/**
		The foreign keys of the table.

		The only way to get the list of the foreign keys of an SQLite
		table is through the PRAGMA foreign_key_list command, we
		cannot get informations about individual foreign keys.
	*/

	protected $aForeignKeys;

	/**
		The columns of the primary key.

		The primary key is fetched from the database when
		fetching the list of the columns of the table.
	*/

	protected $aPrimaryKey = array();

	/**
		Initialises a new sqlite table object.

		This class should NEVER be instantiated manually.
		Instances of this class should be returned by weeSQLiteDbMeta.

		@param	$oMeta	The sqlite dbmeta object.
		@param	$aData	The object data.
	*/

	public function __construct(weeSQLiteDbMeta $oMeta, array $aData)
	{
		parent::__construct($oMeta, $aData);
	}

	/**
		Returns a column of the table.

		@param	$sName						The column name.
		@return	weeSQLiteDbMetaColumn		The column.
		@throw	UnexpectedValueException	The column does not exist.
	*/

	public function column($sName)
	{
		$aColumns = $this->queryColumns();
		isset($aColumns[$sName])
			or burn('UnexpectedValueException',
				sprintf(_WT('Column "%s" does not exist.'), $sName));
		return $this->instantiateObject($this->getColumnClass(), $aColumns[$sName]);
	}

	/**
		Returns whether a given column exists in the table.

		@param	$sName	The column name.
		@return	bool	Whether the column exists in the table.
	*/

	public function columnExists($sName)
	{
		$aColumns = $this->queryColumns();
		return isset($aColumns[$sName]);
	}

	/**
		Returns a foreign key of a given name.

		@param	$sName						The name of the foreign key.
		@return	weeSQLiteDbMetaPrimaryKey	The foreign key.
		@throw	UnexpectedValueException	The foreign key does not exist.
	*/

	public function foreignKey($sName)
	{
		$aForeignKeys = $this->queryForeignKeys();
		isset($aForeignKeys[$sName])
			or burn('UnexpectedValueException',
				sprintf(_WT('Foreign key "%s" does not exist.'), $sName));
		return $this->instantiateObject($this->getForeignKeyClass(), $aForeignKeys[$sName]);
	}

	/**
		Returns whether a foreign key of a given name exists.

		@param	$sName	The name of the table.
		@return	bool	Whether the foreign key exists.
	*/

	public function foreignKeyExists($sName)
	{
		$aForeignKeys = $this->queryForeignKeys();
		return isset($aForeignKeys[$sName]);
	}

	/**
		Returns all the foreign keys.

		@return	array(weeSQLiteDbMetaPrimaryKey)	The array of foreign keys.
	*/

	public function foreignKeys()
	{
		$aForeignKeys	= array();
		$sClass			= $this->getForeignKeyClass();
		foreach ($this->queryForeignKeys() as $aForeignKey)
			$aForeignKeys[] = $this->instantiateObject($sClass, $aForeignKey);
		return $aForeignKeys;
	}

	/**
		Returns the name of the column class.

		@return	string	The name of the column class.
	*/

	public function getColumnClass()
	{
		return 'weeSQLiteDbMetaColumn';
	}

	/**
		Returns the name of the foreign key class.

		@return	string	The name of the foreign key class.
	*/

	public function getForeignKeyClass()
	{
		return 'weeSQLiteDbMetaForeignKey';
	}

	/**
		Returns the name of the primary key class.

		@return	string	The name of the primary key class.
	*/

	public function getPrimaryKeyClass()
	{
		return 'weeSQLiteDbMetaPrimaryKey';
	}

	/**
		Returns whether the table has a primary key.

		@return	bool	Whether the table has a primary key.
	*/

	public function hasPrimaryKey()
	{
		$this->queryColumns();
		return !empty($this->aPrimaryKey);
	}

	/**
		Returns the primary key of the table.

		@return	weeSQLiteDbMetaPrimaryKey	The primary key of the table.
		@throw	IllegalStateException		The table does not have a primary key.
	*/

	public function primaryKey()
	{
		$this->queryColumns();
		!empty($this->aPrimaryKey)
			or burn('IllegalStateException',
				_WT('The table does not have a primary key.'));

		return $this->instantiateObject($this->getPrimaryKeyClass(), array(
			'columns' => $this->aPrimaryKey
		));
	}

	/**
		Returns the names of the columns of the primary key of the table.

		As the columns taking part in the primary key are known by the table instance,
		we provide a shortcut method to access these informations without
		creating a new weeSQLiteDbMetaPrimaryKey instance. This shortcut is used
		by the weeSQLiteDbMetaColumn::getValidator to check if the column is
		"INTEGER PRIMARY KEY".

		@return	array(string)			The names of the columns of the primary key of the table.
		@throw	IllegalStateException	The table does not have a primary key.
	*/

	public function primaryKeyColumnsNames()
	{
		$this->queryColumns();
		!empty($this->aPrimaryKey)
			or burn('IllegalStateException',
				_WT('The table does not have a primary key.'));
		return $this->aPrimaryKey;
	}

	/**
		Queries all the foreign keys of the table.

		@return	array	The data of all the foreign keys of the table.
	*/

	protected function queryForeignKeys()
	{
		if ($this->aForeignKeys === null)
		{
			$oForeignKeys = $this->db()->query('
				PRAGMA foreign_key_list(' . $this->quotedName() . ')
			');

			$this->aForeignKeys = array();
			foreach ($oForeignKeys as $aForeignKey)
			{
				if (!isset($this->aForeignKeys[$aForeignKey['id']]))
					$this->aForeignKeys[$aForeignKey['id']] = array(
						'name'					=> $aForeignKey['id'],
						'table'					=> $this->aData['name'],
						'referenced_table'		=> $aForeignKey['table'],
						'columns'				=> array($aForeignKey['from']),
						'referenced_columns'	=> array($aForeignKey['to'])
					);
				else
				{
					$this->aForeignKeys[$aForeignKey['id']]['columns'][] = $aForeignKey['from'];
					$this->aForeignKeys[$aForeignKey['id']]['referenced_columns'][] = $aForeignKey['to'];
				}
			}
		}

		return $this->aForeignKeys;
	}

	/**
		Queries all the columns of the table.

		@return	array	The data of all the columns of the table.
	*/

	protected function queryColumns()
	{
		if ($this->aColumns === null)
		{
			$oColumns = $this->db()->query('
				PRAGMA table_info(' . $this->quotedName() . ')
			');

			$this->aColumns = array();
			foreach ($oColumns as $aColumn)
			{
				$this->aColumns[$aColumn['name']] = array(
					'name'		=> $aColumn['name'],
					'num'		=> $aColumn['cid'],
					'nullable'	=> !$aColumn['notnull'],
					'default'	=> $aColumn['dflt_value'],
					'type'		=> $aColumn['type'],
				);

				if ($aColumn['pk'])
					$this->aPrimaryKey[] = $aColumn['name'];
			}
		}

		return $this->aColumns;
	}
}
