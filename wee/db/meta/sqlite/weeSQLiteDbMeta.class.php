<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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
	SQLite driver of the weeDbMeta class.
*/

class weeSQLiteDbMeta extends weeDbMeta
{
	/**
		Initialises a new SQLite database meta.

		@param	$oDb						The database to query.
		@throw	InvalidArgumentException	$oDb is not an instance of weeSQLiteDatabase nor a sqlite or sqlite3 instance of weePDODatabase.
	*/

	public function __construct(weeDatabase $oDb)
	{
		$oDb instanceof weeSQLiteDatabase || $oDb instanceof weePDODatabase && in_array($oDb->getDriverName(), array('sqlite', 'sqlite2'))
			or burn('InvalidArgumentException',
				_WT('$oDb must be an instance of weePgSQLDatabase or a sqlite or sqlite2 instance of weeSQLiteDatabase.'));
		parent::__construct($oDb);
	}

	/**
		Returns the name of the table class.

		@return	string	The name of the table class.
	*/

	public function getTableClass()
	{
		return 'weeSQLiteDbMetaTable';
	}

	/**
		Returns a table of a given name in the database.

		@param	$sName						The name of the table.
		@return	weeSQLiteDbMetaTable		The table.
		@throw	UnexpectedValueException	The table does not exist.
	*/

	public function table($sName)
	{
		$this->tableExists($sName)
			or burn('UnexpectedValueException',
				sprintf(_WT('Table "%s" does not exist.'), $sName));

		$sClass = $this->getTableClass();
		return new $sClass($this, array('name' => $sName));
	}

	/**
		Returns whether a table of a given name exists in the database.

		@param	$sName	The name of the table.
		@return	bool	true if the table exists in the database, false otherwise.
	*/

	public function tableExists($sName)
	{
		return (bool)$this->db()->queryValue("
			SELECT COUNT(*) FROM sqlite_master
			WHERE type = 'table' AND name = ?
			LIMIT 1
		", $sName);
	}

	/**
		Returns all the tables of the database.

		@return	array(weeSQLiteDbMetaTable)	The array of tables.
	*/

	public function tables()
	{
		$oQuery = $this->db()->query("
			SELECT name FROM sqlite_master
			WHERE type = 'table'
			ORDER BY name
		");

		$aTables	= array();
		$sClass		= $this->getTableClass();
		foreach ($oQuery as $aTable)
			$aTables[] = new $sClass($this, $aTable);
		return $aTables;
	}
}
