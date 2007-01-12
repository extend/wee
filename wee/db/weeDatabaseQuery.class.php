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

class weeDatabaseQuery
{
	public static	$criteriaClass;
	public static	$queryClass;

	protected		$sAction;
	protected		$oCriteria;
	protected		$sTable;
	protected		$aValues = array();

	public function build($oDatabase)
	{
		$sMethod = 'build' . $this->sAction;
		fire(!is_callable(array($this, $sMethod)), 'IllegalStateException');
		return $this->$sMethod($oDatabase);
	}

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

	public function insert($sTable)
	{
		$this->sAction	= 'Insert';
		$this->sTable	= $sTable;
		return $this;
	}

	public function set($sName, $sValue)
	{
		if ($sName[0] == '`')
			$sName = substr($sName, 1, -1);

		$this->aValues[$sName] = $sValue;
		return $this;
	}

	public function update($sTable)
	{
		$this->sAction	= 'Update';
		$this->sTable	= $sTable;
		return $this;
	}

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
