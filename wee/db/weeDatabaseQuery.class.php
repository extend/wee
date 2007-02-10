<?php

/*
	Web:Extend
	Copyright (c) 2006 Dev:Extend

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
	Generate database queries.

	Used by weeForm to create INSERT or UPDATE queries based on the data received.
*/

class weeDatabaseQuery
{
	/**
		Name of the weeDatabaseCriteria to use.
		Used by § to create a new criteria.

		@bug When using more than one database.
	*/

	public static	$criteriaClass;

	/**
		Name of the weeDatabaseQuery to use.
		Used by § to create a new query.

		@bug When using more than one database.
	*/

	public static	$queryClass;

	/**
		Wether Insert or Update.
		Determine which type of query will be built.
	*/

	protected		$sAction;

	/**
		Criteria for this query.
		Basically, a criteria is the WHERE part.
		It is created using the weeDatabaseCriteria class.
	*/

	protected		$oCriteria;

	/**
		Table where data will be inserted or updated.
	*/

	protected		$sTable;

	/**
		Values to insert or update.
	*/

	protected		$aValues = array();

	/**
		Build the query.

		You do not need to use this method, the weeDatabase object will call it for you.

		@param	$oDatabase	The database object. Used to access to the escape method.
		@return	string		The SQL query.
	*/

	public function build($oDatabase)
	{
		$sMethod = 'build' . $this->sAction;
		fire(!is_callable(array($this, $sMethod)), 'IllegalStateException');
		return $this->$sMethod($oDatabase);
	}

	/**
		Build an insert query.

		@param	$oDatabase	The database object. Used to access to the escape method.
		@return	string		The SQL query.
	*/

	protected function buildInsert($oDatabase)
	{
		$sColumns = null;
		$sValues = null;

		foreach ($this->aValues as $sName => $sValue)
		{
			if (empty($sValue) || $sValue[0] != '`')//TODO:possible postgresql bug here: `
				$sValue	= $oDatabase->escape($sValue);

			$sColumns .= $sName . ',';
			$sValues .= $sValue . ',';
		}

		return 'INSERT INTO ' . $this->sTable . ' (' . substr($sColumns, 0, -1) . ') VALUES (' . substr($sValues, 0, -1) . ')';
	}

	/**
		Build an update query.

		@param	$oDatabase	The database object. Used to access to the escape method.
		@return	string		The SQL query.
	*/

	protected function buildUpdate($oDatabase)
	{
		$sSQL = 'UPDATE ' . $this->sTable . ' SET ';

		foreach ($this->aValues as $sName => $sValue)
		{
			if (empty($sValue) || $sValue[0] != '`')//TODO:possible postgresql bug here: `
				$sValue	= $oDatabase->escape($sValue);
			$sSQL .= $sName . '=' . $sValue . ',';
		}

		return substr($sSQL, 0, -1) . ' WHERE ' . $this->oCriteria->build($oDatabase);
	}

	/**
		Initialize an insert query.

		Usually you do not need to use this method, the weeDatabase object will call it for you.

		@param	$sTable	The table to insert to.
		@return	$this
	*/

	public function insert($sTable)
	{
		$this->sAction	= 'Insert';
		$this->sTable	= $sTable;
		return $this;
	}

	/**
		Add a value to insert or update.
		If a value was already given for a name, replace it.

		@param	$sName	Name of the column.
		@param	$sValue	Value to write in the column.
		@return	$this
	*/

	public function set($sName, $sValue)
	{
		if ($sName[0] == '`')
			$sName = substr($sName, 1, -1);

		$this->aValues[$sName] = $sValue;
		return $this;
	}

	/**
		Initialize an update query.

		Usually you do not need to use this method, the weeDatabase object will call it for you.

		@param	$sTable	The table to update to.
		@return	$this
	*/

	public function update($sTable)
	{
		$this->sAction	= 'Update';
		$this->sTable	= $sTable;
		return $this;
	}

	/**
		Sets or retrieve the criteria for this query.
		Criteria are used only for UPDATE queries.

		If no criteria object is specified and it wasn't set before: return a new object.
		If no criteria object is specified but one was set before: return it.
		Else, save the criteria object given.

		@param	$oWhereCriteria		The weeDatabaseCriteria object for this query.
		@return	weeDatabaseCriteria	Returns the current weeDatabaseCriteria if no new one is given.
		@return	$this				If a new one is given.
	*/

	public function where($oWhereCriteria = null)
	{
		fire(empty(self::$criteriaClass), 'IllegalStateException');
		fire(!is_object($oWhereCriteria), 'UnexpectedValueException');

		if (is_null($oWhereCriteria))
		{
			if (empty($this->oCriteria))
				$this->oCriteria = new self::$criteriaClass;
			return $this->oCriteria;
		}
		else
		{
			fire(!empty($this->oCriteria), 'IllegalStateException');
			$this->oCriteria = $oWhereCriteria;
			return $this;
		}
	}
}

/**
	Magic function to help with the database query creation.

	If no argument is given, return a new weeDatabaseQuery object.
	Else, return a new weeCriteriaObject, created with the arguments given.

	@return	weeDatabaseQuery	A new query if no argument was given.
	@return	weeDatabaseCriteria	A new criteria if at least one argument was given.
	@see	weeDatabaseCriteria
*/

function §()
{
	if (func_num_args() == 0)
		return new weeDatabaseQuery::$queryClass;
	else
	{
		$s = 'return new weeDatabaseQuery::$criteriaClass(';
		foreach (func_get_args() as $mArg)
			$s .= "'" . $mArg . "',";
		$s = substr($s, 0, -1) . ');';

		return eval($s);
	}
}

?>
